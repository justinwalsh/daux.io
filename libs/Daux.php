<?php namespace Todaymade\Daux;

use Symfony\Component\Console\Output\NullOutput;
use Todaymade\Daux\Tree\Builder;
use Todaymade\Daux\Tree\Root;

class Daux
{
    const STATIC_MODE = 'DAUX_STATIC';
    const LIVE_MODE = 'DAUX_LIVE';

    /** @var string[] */
    public static $VALID_MARKDOWN_EXTENSIONS;

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

        $this->generateTree();
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
            'valid_markdown_extensions' => ['md', 'markdown']
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

        static::$VALID_MARKDOWN_EXTENSIONS = $default_config['valid_markdown_extensions'];

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
    private function generateTree()
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
        $default = [
            //Features
            'multilanguage' => !empty($this->options['languages']),

            //Paths and tree
            'theme-name' => $this->options['theme'],
            'mode' => $this->mode,
            'local_base' => $this->local_base,
            'docs_path' => $this->docs_path,
            'templates' => $this->internal_base . DS . 'templates',
        ];
        $this->options->conservativeMerge($default);

        if ($this->tree) {
            $this->options['tree'] = $this->tree;
            $this->options['index'] = $this->tree->getIndexPage() ?: $this->tree->getFirstPage();
            if ($this->options['multilanguage']) {
                foreach ($this->options['languages'] as $key => $name) {
                    $this->options['entry_page'][$key] = $this->tree->getEntries()[$key]->getFirstPage();
                }
            } else {
                $this->options['entry_page'] = $this->tree->getFirstPage();
            }
        }

        $this->options['index_key'] = 'index.html';
        $this->options['base_page'] = $this->options['base_url'] = '';

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
    }
}
