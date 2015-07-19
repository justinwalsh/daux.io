<?php namespace Todaymade\Daux;

use Symfony\Component\Console\Output\NullOutput;
use Todaymade\Daux\Tree\Builder;
use Todaymade\Daux\Tree\Root;

class Daux
{
    const STATIC_MODE = 'DAUX_STATIC';
    const LIVE_MODE = 'DAUX_LIVE';

    /** @var string */
    public $local_base;

    /** @var string */
    public $internal_base;

    /** @var string */
    private $docs_path;

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
    }

    public static function initConstants()
    {
        define("DS", DIRECTORY_SEPARATOR);
    }

    /**
     * @param string $override_file
     * @throws Exception
     */
    public function initialize($override_file = 'config.json')
    {
        //global.json (docs dir, markdown files)
        $this->loadConfig();

        //config.json
        $this->loadConfigOverrides($override_file);
    }

    /**
     * Load and validate the global configuration
     *
     * @throws Exception
     */
    private function loadConfig()
    {
        $default_config = [
            'docs_directory' => 'docs',
            'valid_content_extensions' => ['md', 'markdown']
        ];

        $global_config_file = $this->local_base . DS . 'global.json';

        if (!file_exists($global_config_file)) {
            throw new Exception('The Global Config file is missing. Requested File : ' . $global_config_file);
        }

        $default_config = array_merge($default_config, json_decode(file_get_contents($global_config_file), true));
        if (!isset($default_config)) {
            throw new Exception('The Global Config file is corrupt. Check that the JSON encoding is correct');
        }

        $this->docs_path = $default_config['docs_directory'];
        if (!is_dir($this->docs_path) && !is_dir($this->docs_path = $this->local_base . DS . $this->docs_path)) {
            throw new Exception('The Docs directory does not exist. Check the path again : ' . $this->docs_path);
        }

        $this->options = new Config();
        $this->options->merge($default_config);
    }

    /**
     * Load the configuration files, first, "config.json"
     * in the documentation and then the file specified
     * when running the configuration
     *
     * @param string $override_file
     * @throws Exception
     */
    private function loadConfigOverrides($override_file)
    {
        // Read documentation overrides
        $config_file = $this->docs_path . DS . 'config.json';
        if (file_exists($config_file)) {
            $config = json_decode(file_get_contents($config_file), true);
            if (!isset($config)) {
                throw new Exception('The local config file is missing. Check path : ' . $config_file);
            }
            $this->options->merge($config);
        }

        // Read command line overrides
        $config_file = $this->local_base . DS . $override_file;
        if (!is_null($override_file) && file_exists($config_file)) {
            $config = json_decode(file_get_contents($config_file), true);
            if (!isset($config)) {
                throw new Exception('The local config file is missing. Check path : ' . $config_file);
            }
            $this->options->merge($config);
        }

        if (isset($this->options['timezone'])) {
            date_default_timezone_set($this->options['timezone']);
        } elseif (!ini_get('date.timezone')) {
            date_default_timezone_set('GMT');
        }
    }

    /**
     * Generate the tree that will be used
     */
    public function generateTree()
    {
        $this->tree = new Root($this->getParams(), $this->docs_path);
        Builder::build($this->tree, $this->options['ignore']);

        if (!empty($this->options['languages'])) {
            foreach ($this->options['languages'] as $key => $node) {
                $this->tree->getEntries()[$key]->setTitle($node);
            }
        }
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
                'templates' => $this->internal_base . DS . 'templates',
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

        //TODO :: check that it implements processor

        return $class;
    }

    /**
     * @return \Todaymade\Daux\Format\Base\Generator
     */
    public function getGenerator()
    {
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

        return new $class($this);
    }
}
