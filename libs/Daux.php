<?php namespace Todaymade\Daux;

    use Todaymade\Daux\Server\Helper as ServerHelper;
    use Todaymade\Daux\Generator\Helper as GeneratorHelper;
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
        public $tree;
        public $options;
        private $mode;

        public function __construct($mode) {
            $this->mode = $mode;

            $this->local_base = dirname(dirname(__FILE__));
            $this->base_url = '';

            if ($this->mode == Daux::LIVE_MODE) {
                $this->host = $_SERVER['HTTP_HOST'];
                $this->base_url = $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
                $t = strrpos($this->base_url, '/index.php');
                if ($t != FALSE) $this->base_url = substr($this->base_url, 0, $t);
                if (substr($this->base_url, -1) !== '/') $this->base_url .= '/';
            }
        }

        public function initialize($global_config_file = null, $config_file = 'config.json') {
            $this->load_global_config($global_config_file);
            $this->load_docs_config($config_file);
            $this->generate_directory_tree();
        }

        private function load_global_config($global_config_file) {
            if (is_null($global_config_file)) $global_config_file = $this->local_base . DIRECTORY_SEPARATOR . 'global.json';
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
            if (!is_dir($this->docs_path) && !is_dir($this->docs_path = $this->local_base . DIRECTORY_SEPARATOR . $this->docs_path)) {
                throw new Exception('The Docs directory does not exist. Check the path again : ' . $this->docs_path);
            }

            if (!isset($global_config['valid_markdown_extensions'])) static::$VALID_MARKDOWN_EXTENSIONS = array('md', 'markdown');
            else static::$VALID_MARKDOWN_EXTENSIONS = $global_config['valid_markdown_extensions'];
        }

        private function load_docs_config($config_file) {
            $config_file = $this->docs_path . DIRECTORY_SEPARATOR . $config_file;
            if (!file_exists($config_file)) {
                throw new Exception('The local config file is missing. Check path : ' . $config_file);
            }
            $this->options = json_decode(file_get_contents($this->local_base . DIRECTORY_SEPARATOR . 'default.json'), true);
            if (is_file($config_file)) {
                $config = json_decode(file_get_contents($config_file), true);
                if (!isset($config)) {
                    throw new Exception('There was an error parsing the Config file. Please review');
                }
                $this->options = array_merge($this->options, $config);
            }
            if (isset($this->options['timezone'])) date_default_timezone_set($this->options['timezone']);
            else if (!ini_get('date.timezone')) date_default_timezone_set('GMT');
        }

        private function generate_directory_tree() {
            $this->tree = Builder::build($this->docs_path, $this->options['ignore'], $this->mode);
            if (!empty($this->options['languages'])) {
                foreach ($this->options['languages'] as $key => $node) {
                    $this->tree->value[$key]->title = $node;
                }
            }
        }

        public function get_base_params() {
            $params = array(
                //Informations
                'tagline' => $this->options['tagline'],
                'title' => $this->options['title'],
                'author' => $this->options['author'],
                'image' => $this->options['image'],
                'repo' => $this->options['repo'],
                'links' => $this->options['links'],
                'twitter' => $this->options['twitter'],

                //Features
                'google_analytics' => ($g = $this->options['google_analytics']) ?  DauxHelper::google_analytics($g, $this->host) : '',
                'piwik_analytics' => ($p = $this->options['piwik_analytics']) ? DauxHelper::piwik_analytics($p, $this->options['piwik_analytics_id']) : '',
                'toggle_code' => $this->options['toggle_code'],
                'float' => $this->options['float'],
                'date_modified' => $this->options['date_modified'],
                'file_editor' => false,
                'breadcrumbs' => $this->options['breadcrumbs'],
                'breadcrumb_separator' => $this->options['breadcrumb_separator'],
                'multilanguage' => !empty($this->options['languages']),
                'languages' => $this->options['languages'],


                //Paths and tree
                'mode' => $this->mode,
                'local_base' => $this->local_base,
                'docs_path' => $this->docs_path,
                'tree' => $this->tree,
                'index' => ($this->tree->index_page !== false) ? $this->tree->index_page : $this->tree->first_page,
                'template' => $this->options['template'],
            );

            if (!$params['multilanguage']) {
                foreach ($this->options['languages'] as $key => $name) {
                    $params['entry_page'][$key] = $this->tree->value[$key]->first_page;
                }
            } else {
                $params['entry_page'] = $this->tree->first_page;
            }

            return $params;
        }

        //TODO :: move to generator
        public function get_page_params() {
            $params = $this->get_base_params();

            $params['index_key'] = 'index.html';
            $params['base_page'] = $params['base_url'] = '';

            $params['theme'] = DauxHelper::get_theme(
                $this->local_base . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $this->options['template'] . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . $this->options['theme'],
                $params['base_url'],
                $this->local_base,
                $params['base_url'] . "templates/" . $params['template'] . "/themes/" . $this->options['theme'] . '/'
            );

            return $params;
        }

        //TODO :: move to server
        public function get_live_page_params() {
            $params = $this->get_base_params();

            $params['index_key'] = 'index';
            $params['host'] = $this->host;
            $params['base_page'] = $params['base_url'] = '//' . $this->base_url;
            if (!$this->options['clean_urls']) $params['base_page'] .= 'index.php/';

            if ($params['image'] !== '') $params['image'] = str_replace('<base_url>', $params['base_url'], $params['image']);

            $params['theme'] = DauxHelper::get_theme(
                $this->local_base . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $this->options['template'] . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . $this->options['theme'],
                $params['base_url'],
                $this->local_base,
                $params['base_url'] . "templates/" . $params['template'] . "/themes/" . $this->options['theme'] . '/'
            );

            return $params;
        }
    }
