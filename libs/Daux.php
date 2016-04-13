<?php namespace Todaymade\Daux;

use Symfony\Component\Console\Output\NullOutput;
use Todaymade\Daux\ContentTypes\ContentTypeHandler;
use Todaymade\Daux\Tree\Builder;
use Todaymade\Daux\Tree\Content;
use Todaymade\Daux\Tree\Directory;
use Todaymade\Daux\Tree\Root;

class Daux
{
    const STATIC_MODE = 'DAUX_STATIC';
    const LIVE_MODE = 'DAUX_LIVE';

    /** @var string */
    public $local_base;

    /** @var string */
    public $internal_base;

    /** @var \Todaymade\Daux\Format\Base\Generator */
    protected $generator;

    /** @var ContentTypeHandler */
    protected $typeHandler;

    /**
     * @var string[]
     */
    protected $validExtensions;

    /** @var string */
    private $docs_path;

    /** @var string */
    private $themes_path;

    /** @var Processor */
    protected $processor;

    /** @var Tree\Root */
    public $tree;

    /** @var Config */
    public $options;

    /** @var string */
    private $mode;

    /** @var bool */
    private $merged_defaults = false;

    /** @var bool */
    private $merged_tree = false;

    /**
     * @param string $mode
     */
    public function __construct($mode)
    {
        $this->mode = $mode;

        $this->local_base = $this->internal_base = dirname(__DIR__);

        // In case we're inside the phar archive
        // we save the path to the directory
        // in which it is contained
        if (defined('PHAR_DIR')) {
            $this->local_base = PHAR_DIR;
        }

        // global.json
        $this->loadBaseConfiguration();
    }

    /**
     * @param string $override_file
     * @throws Exception
     */
    public function initializeConfiguration($override_file = 'config.json')
    {
        // Read documentation overrides
        $this->loadConfiguration($this->docs_path . DIRECTORY_SEPARATOR . 'config.json');

        // Read command line overrides
        if (!is_null($override_file)) {
            $this->loadConfiguration($this->local_base . DIRECTORY_SEPARATOR . $override_file);
        }

        // Set a valid default timezone
        if (isset($this->options['timezone'])) {
            date_default_timezone_set($this->options['timezone']);
        } elseif (!ini_get('date.timezone')) {
            date_default_timezone_set('GMT');
        }
    }

    public function setThemesPath($path)
    {
        $this->themes_path = $path;
        if (!is_dir($this->themes_path) &&
            !is_dir($this->themes_path = $this->local_base . DIRECTORY_SEPARATOR . $this->themes_path)
        ) {
            throw new Exception('The Themes directory does not exist. Check the path again : ' . $this->themes_path);
        }
        $this->options['themes_path'] = $this->themes_path;
        $this->options['templates'] = 'templates';
    }

    public function setDocumentationPath($path)
    {
        $this->docs_path = $path;
        if (!is_dir($this->docs_path) &&
            !is_dir($this->docs_path = $this->local_base . DIRECTORY_SEPARATOR . $this->docs_path)
        ) {
            throw new Exception('The Docs directory does not exist. Check the path again : ' . $this->docs_path);
        }

        $this->options['docs_path'] = $this->docs_path;
    }

    /**
     * Load and validate the global configuration
     *
     * @throws Exception
     */
    protected function loadBaseConfiguration()
    {
        $this->options = new Config();

        // Set the default configuration
        $this->options->merge([
            'docs_directory' => 'docs',
            'valid_content_extensions' => ['md', 'markdown']
        ]);

        // Load the global configuration
        $this->loadConfiguration($this->local_base . DIRECTORY_SEPARATOR . 'global.json', false);
    }

    /**
     * @param string $config_file
     * @param bool $optional
     * @throws Exception
     */
    protected function loadConfiguration($config_file, $optional = true)
    {
        if (!file_exists($config_file)) {
            if ($optional) {
                return;
            }

            throw new Exception('The configuration file is missing. Check path : ' . $config_file);
        }

        $config = json_decode(file_get_contents($config_file), true);
        if (!isset($config)) {
            throw new Exception('The configuration file "' . $config_file . '" is corrupt. Is your JSON well-formed ?');
        }
        $this->options->merge($config);
    }

    /**
     * Generate the tree that will be used
     */
    public function generateTree()
    {
        $this->options['valid_content_extensions'] = $this->getContentExtensions();

        $this->tree = new Root($this->getParams(), $this->docs_path);
        Builder::build($this->tree, $this->options['ignore']);

        if (!empty($this->options['languages'])) {
            foreach ($this->options['languages'] as $key => $node) {
                $this->tree->getEntries()[$key]->setTitle($node);
            }
        }

        // Enhance the tree with processors
        $this->getProcessor()->manipulateTree($this->tree);

        // Sort the tree one last time before it is finalized
        $this->sortTree($this->tree);

        $this->finalizeTree($this->tree);
    }

    public function sortTree(Directory $current)
    {
        $current->sort();
        foreach ($current->getEntries() as $entry) {
            if ($entry instanceof Directory) {
                $this->sortTree($entry);
            }
        }
    }

    public function finalizeTree(Directory $current, $prev = null)
    {
        foreach ($current->getEntries() as $entry) {
            if ($entry instanceof Directory) {
                $prev = $this->finalizeTree($entry, $prev);
            } elseif ($entry instanceof Content) {
                if ($prev) {
                    $prev->setNext($entry);
                    $entry->setPrevious($prev);
                }

                $prev = $entry;
            }
        }

        return $prev;
    }

    /**
     * @return Config
     */
    public function getParams()
    {
        if (!$this->merged_defaults) {
            $default = [
                //Features
                'multilanguage' => !empty($this->options['languages']),

                //Paths and tree
                'mode' => $this->mode,
                'local_base' => $this->local_base,
                'docs_path' => $this->docs_path,
                'themes_path' => $this->themes_path,
                'templates' => 'templates'
            ];
            $this->options->conservativeMerge($default);

            $this->options['index_key'] = 'index.html';
            $this->options['base_page'] = $this->options['base_url'] = '';

            $this->merged_defaults = true;
        }

        if ($this->tree && !$this->merged_tree) {
            $this->options['tree'] = $this->tree;
            $this->options['index'] = $this->tree->getIndexPage() ?: $this->tree->getFirstPage();
            if ($this->options['multilanguage']) {
                foreach ($this->options['languages'] as $key => $name) {
                    $this->options['entry_page'][$key] = $this->tree->getEntries()[$key]->getFirstPage();
                }
            } else {
                $this->options['entry_page'] = $this->tree->getFirstPage();
            }
            $this->merged_tree = true;
        }

        return $this->options;
    }

    /**
     * @return Processor
     */
    public function getProcessor()
    {
        if (!$this->processor) {
            $this->processor = new Processor($this, new NullOutput(), 0);
        }

        return $this->processor;
    }

    /**
     * @param Processor $processor
     */
    public function setProcessor(Processor $processor)
    {
        $this->processor = $processor;

        // This is not the cleanest but it's
        // the best i've found to use the
        // processor in very remote places
        $this->options['processor_instance'] = $processor;
    }

    public function getGenerators()
    {
        $default = [
            'confluence' => '\Todaymade\Daux\Format\Confluence\Generator',
            'html-file' => '\Todaymade\Daux\Format\HTMLFile\Generator',
            'html' => '\Todaymade\Daux\Format\HTML\Generator',
        ];

        $extended = $this->getProcessor()->addGenerators();

        return array_replace($default, $extended);
    }


    public function getProcessorClass()
    {
        $processor = $this->getParams()['processor'];

        if (empty($processor)) {
            return null;
        }

        $class = "\\Todaymade\\Daux\\Extension\\" . $processor;
        if (!class_exists($class)) {
            throw new \RuntimeException("Class '$class' not found. We cannot use it as a Processor");
        }

        if (!array_key_exists("Todaymade\\Daux\\Processor", class_parents($class))) {
            throw new \RuntimeException("Class '$class' invalid, should extend '\\Todaymade\\Daux\\Processor'");
        }

        return $class;
    }

    /**
     * @return \Todaymade\Daux\Format\Base\Generator
     */
    public function getGenerator()
    {
        if ($this->generator) {
            return $this->generator;
        }

        $generators = $this->getGenerators();

        $format = $this->getParams()['format'];

        if (!array_key_exists($format, $generators)) {
            throw new \RuntimeException("The format '$format' doesn't exist, did you forget to set your processor ?");
        }

        $class = $generators[$format];
        if (!class_exists($class)) {
            throw new \RuntimeException("Class '$class' not found. We cannot use it as a Generator");
        }

        $interface = 'Todaymade\Daux\Format\Base\Generator';
        if (!in_array('Todaymade\Daux\Format\Base\Generator', class_implements($class))) {
            throw new \RuntimeException("The class '$class' does not implement the '$interface' interface");
        }

        return $this->generator = new $class($this);
    }

    public function getContentTypeHandler()
    {
        if ($this->typeHandler) {
            return $this->typeHandler;
        }

        $base_types = $this->getGenerator()->getContentTypes();

        $extended = $this->getProcessor()->addContentType();

        $types = array_merge($base_types, $extended);

        return $this->typeHandler = new ContentTypeHandler($types);
    }

    /**
     * Get all content file extensions
     *
     * @return string[]
     */
    public function getContentExtensions()
    {
        if ($this->validExtensions) {
            return $this->validExtensions;
        }

        return $this->validExtensions = $this->getContentTypeHandler()->getContentExtensions();
    }
}
