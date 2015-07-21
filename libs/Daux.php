<?php namespace Todaymade\Daux;

    use Todaymade\Daux\Tree\Builder;

class Daux
{
    const STATIC_MODE = 'DAUX_STATIC';
    const LIVE_MODE = 'DAUX_LIVE';

    public static $VALID_MARKDOWN_EXTENSIONS;
    public $local_base;
    public $base_url = '';
    public $host;
    private $docs_path;

    /**
     * @var Tree\Entry
     */
    public $tree;
    public $options;
    private $mode;

    public function __construct($mode)
    {
        $this->mode = $mode;

        // LOCAL_BASE may be defined from caller to prevent symlink issues
        $this->local_base = defined('LOCAL_BASE') ? LOCAL_BASE : dirname(__DIR__);
        $this->base_url = '';

        if ($this->mode == Daux::LIVE_MODE) {
            $this->host = $_SERVER['HTTP_HOST'];
            $this->base_url = $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
            $t = strrpos($this->base_url, '/index.php');
            if ($t != false) {
                $this->base_url = substr($this->base_url, 0, $t);
            }
            if (substr($this->base_url, -1) !== '/') {
                $this->base_url .= '/';
            }
        }
    }

    public static function initConstants()
    {
        define("DS", DIRECTORY_SEPARATOR);
    }

    public function initialize($global_config_file = null, $config_file = 'config.json')
    {
        $this->loadConfig($global_config_file);
        $this->loadConfigOverrides($config_file);
        $this->generateTree();
    }

    private function loadConfig($global_config_file)
    {
        if (is_null($global_config_file)) {
            $global_config_file = $this->local_base . DS . 'global.json';
        }
        if (!file_exists($global_config_file)) {
            throw new Exception('The Global Config file is missing. Requested File : ' . $global_config_file);
        }

        $global_config = json_decode(file_get_contents($global_config_file), true);
        if (!isset($global_config)) {
            throw new Exception('The Global Config file is corrupt. Check that the JSON encoding is correct');
        }

        if (!isset($global_config['docs_directory'])) {
            throw new Exception('The Global Config file does not have the docs directory set.');
        }

        $this->docs_path = $global_config['docs_directory'];
        if (!is_dir($this->docs_path) && !is_dir($this->docs_path = $this->local_base . DS . $this->docs_path)) {
            throw new Exception('The Docs directory does not exist. Check the path again : ' . $this->docs_path);
        }

        if (!isset($global_config['valid_markdown_extensions'])) {
            static::$VALID_MARKDOWN_EXTENSIONS = array('md', 'markdown');
        } else {
            static::$VALID_MARKDOWN_EXTENSIONS = $global_config['valid_markdown_extensions'];
        }
    }

    private function loadConfigOverrides($config_file)
    {
        $config_file = $this->docs_path . DS . $config_file;
        if (!file_exists($config_file)) {
            throw new Exception('The local config file is missing. Check path : ' . $config_file);
        }
        $this->options = json_decode(file_get_contents($this->local_base . DS . 'default.json'), true);
        if (is_file($config_file)) {
            $config = json_decode(file_get_contents($config_file), true);
            if (!isset($config)) {
                throw new Exception('There was an error parsing the Config file. Please review');
            }
            $this->options = array_merge($this->options, $config);
        }
        if (isset($this->options['timezone'])) {
            date_default_timezone_set($this->options['timezone']);
        } elseif (!ini_get('date.timezone')) {
            date_default_timezone_set('GMT');
        }
    }

    private function generateTree()
    {
        $this->tree = Builder::build($this->docs_path, $this->options['ignore'], $this->getParams());
        if (!empty($this->options['languages'])) {
            foreach ($this->options['languages'] as $key => $node) {
                $this->tree->value[$key]->title = $node;
            }
        }
    }

    public function getParams()
    {
        $params = $this->options += array(
            //Features
            'multilanguage' => !empty($this->options['languages']),

            //Paths and tree
            'theme-name' => $this->options['theme'],
            'mode' => $this->mode,
            'local_base' => $this->local_base,
            'docs_path' => $this->docs_path,
            'templates' => $this->local_base . DS . 'templates',
        );

        if ($this->tree) {
            $params['tree'] = $this->tree;
            $params['index'] = ($index = $this->tree->getIndexPage()) ? $index : $this->tree->getFirstPage();
            if ($params['multilanguage']) {
                foreach ($this->options['languages'] as $key => $name) {
                    $params['entry_page'][$key] = $this->tree->value[$key]->getFirstPage();
                }
            } else {
                $params['entry_page'] = $this->tree->getFirstPage();
            }
        }

        if ($this->mode == self::LIVE_MODE) {
            $params['index_key'] = 'index';
            $params['host'] = $this->host;
            $params['base_page'] = $params['base_url'] = '//' . $this->base_url;
            if (!$this->options['clean_urls']) {
                $params['base_page'] .= 'index.php/';
            }

            if ($params['image'] !== '') {
                $params['image'] = str_replace('<base_url>', $params['base_url'], $params['image']);
            }
        } else {
            $params['index_key'] = 'index.html';
            $params['base_page'] = $params['base_url'] = '';
        }

        $params['theme'] = DauxHelper::getTheme(
            $this->options['theme-name'],
            $params['base_url'],
            $this->local_base,
            $params['base_url']
        );

        return $params;
    }
}
