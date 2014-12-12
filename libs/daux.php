<?php
    namespace Todaymade\Daux;
    require_once(dirname(__FILE__) . '/../vendor/autoload.php');
    require_once('daux_directory.php');
    require_once('daux_helper.php');
    require_once('daux_page.php');


    class Daux
    {
        const STATIC_MODE = 'DAUX_STATIC';
        const LIVE_MODE = 'DAUX_LIVE';

        public static $VALID_MARKDOWN_EXTENSIONS;
        private $local_base;
        private $base_url;
        private $host;
        private $docs_path;
        private $tree;
        private $options;
        private $error_page;
        private $error = false;
        private $params;
        private $mode;

        function __construct($global_config_file = NULL) {
            $this->initial_setup($global_config_file);
        }

        public function initialize($config_file = 'config.json') {
            if ($this->error) return;
            $this->load_docs_config($config_file);
            $this->generate_directory_tree();
            if (!$this->error) $this->params = $this->get_page_params();
        }

        public function generate_static($output_dir = NULL) {
            if (is_null($output_dir)) $output_dir = $this->local_base . DIRECTORY_SEPARATOR . 'static';
            DauxHelper::clean_copy_assets($output_dir, $this->local_base);
            $this->recursive_generate_static($this->tree, $output_dir, $this->params);
        }

        public function handle_request($url, $query = array()) {
            if ($this->error) return $this->error_page;
            if (!$this->params['clean_urls']) $this->params['base_page'] .= 'index.php/';
            $request = DauxHelper::get_request();
            $request = urldecode($request);
            $request_type = isset($query['method']) ? $query['method'] : '';
            if($request == 'first_page') {
                $request = $this->tree->first_page->uri;
            }
            switch ($request_type) {
                case 'DauxEdit':
                    if ($this->options['file_editor']) {
                        $content = isset($query['markdown']) ? $query['markdown'] : '';
                        return $this->save_file($request, $content);
                    }
                    return $this->generate_error_page('Editing Disabled', 'Editing is currently disabled in config',
                        ErrorPage::FATAL_ERROR_TYPE);
                default:
                    return $this->get_page($request);
            }
        }

        private function initial_setup($global_config_file) {
            $this->setup_environment_variables();
            $this->load_global_config($global_config_file);
        }

        private function setup_environment_variables() {
            global $argc;
            $this->local_base = dirname(dirname(__FILE__));
            $this->base_url = '';
            if (isset($argc)) {
                $this->mode = Daux::STATIC_MODE;
                return;
            }
            $this->mode = Daux::LIVE_MODE;
            $this->host = $_SERVER['HTTP_HOST'];
            $this->base_url = $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
            $t = strrpos($this->base_url, '/index.php');
            if ($t != FALSE) $this->base_url = substr($this->base_url, 0, $t);
            if (substr($this->base_url, -1) !== '/') $this->base_url .= '/';
        }

        private function load_global_config($global_config_file) {
            if (is_null($global_config_file)) $global_config_file = $this->local_base . DIRECTORY_SEPARATOR . 'global.json';
            if (!file_exists($global_config_file)) {
                $this->generate_error_page('Global Config File Missing',
                'The Global Config file is missing. Requested File : ' . $global_config_file, ErrorPage::FATAL_ERROR_TYPE);
                return;
            }

            $global_config = json_decode(file_get_contents($global_config_file), true);
            if (!isset($global_config)) {
                $this->generate_error_page('Corrupt Global Config File',
                    'The Global Config file is corrupt. Check that the JSON encoding is correct', ErrorPage::FATAL_ERROR_TYPE);
                return;
            }

            if (!isset($global_config['docs_directory'])) {
                $this->generate_error_page('Docs Directory not set', 'The Global Config file does not have the docs directory set.',
                    ErrorPage::FATAL_ERROR_TYPE);
                return;
            }

            $this->docs_path = $global_config['docs_directory'];
            if (!is_dir($this->docs_path) && !is_dir($this->docs_path = $this->local_base . DIRECTORY_SEPARATOR . $this->docs_path)) {
                $this->generate_error_page('Docs Directory not found',
                    'The Docs directory does not exist. Check the path again : ' . $this->docs_path, ErrorPage::FATAL_ERROR_TYPE);
                return;
            }

            if (!isset($global_config['valid_markdown_extensions'])) static::$VALID_MARKDOWN_EXTENSIONS = array('md', 'markdown');
            else static::$VALID_MARKDOWN_EXTENSIONS = $global_config['valid_markdown_extensions'];
        }

        private function load_docs_config($config_file) {
            $config_file = $this->docs_path . DIRECTORY_SEPARATOR . $config_file;
            if (!file_exists($config_file)) {
                $this->generate_error_page('Config File Missing',
                    'The local config file is missing. Check path : ' . $config_file, ErrorPage::FATAL_ERROR_TYPE);
                return;
            }
            $this->options = json_decode(file_get_contents($this->local_base . DIRECTORY_SEPARATOR . 'default.json'), true);
            if (is_file($config_file)) {
                $config = json_decode(file_get_contents($config_file), true);
                if (!isset($config)) {
                    $this->generate_error_page('Invalid Config File',
                        'There was an error parsing the Config file. Please review', ErrorPage::FATAL_ERROR_TYPE);
                    return;
                }
                $this->options = array_merge($this->options, $config);
            }
            if (isset($this->options['timezone'])) date_default_timezone_set($this->options['timezone']);
            else if (!ini_get('date.timezone')) date_default_timezone_set('GMT');
        }

        private function generate_directory_tree() {
            $this->tree = DauxHelper::build_directory_tree($this->docs_path, $this->options['ignore'], $this->mode);
            if (!empty($this->options['languages'])) {
                foreach ($this->options['languages'] as $key => $node) {
                    $this->tree->value[$key]->title = $node;
                }
            }
        }

        private function recursive_generate_static($tree, $output_dir, $params, $base_url = '') {
            $params['base_url'] = $params['base_page'] = $base_url;
            $new_params = $params;
            //changed this as well in order for the templates to be put in the right place
            $params['theme'] = DauxHelper::rebase_theme($params['theme'], $base_url, $params['base_url'] . "templates/default/themes/" . $params['theme']['name'] . '/');
            //
            $params['image'] = str_replace('<base_url>', $base_url, $params['image']);
            if ($base_url !== '') $params['entry_page'] = $tree->first_page;
            foreach ($tree->value as $key => $node) {
                if ($node->type === Directory_Entry::DIRECTORY_TYPE) {
                    $new_output_dir = $output_dir . DIRECTORY_SEPARATOR . $key;
                    @mkdir($new_output_dir);
                    $this->recursive_generate_static($node, $new_output_dir, $new_params, '../' . $base_url);
                } else {
                    $params['request'] = $node->get_url();
                    $params['file_uri'] = $node->name;

                    $page = MarkdownPage::fromFile($node, $params);
                    file_put_contents($output_dir . DIRECTORY_SEPARATOR . $key, $page->get_page_content());
                }
            }
        }

        private function save_file($request, $content) {
            $file = $this->get_file_from_request($request);
            if ($file === false) return $this->generate_error_page('Page Not Found',
                'The Page you requested is yet to be made. Try again later.', ErrorPage::MISSING_PAGE_ERROR_TYPE);
            if ($file->write($content)) return new SimplePage('Success', 'Successfully Edited');
            else return $this->generate_error_page('File Not Writable', 'The file you wish to write to is not writable.',
                ErrorPage::FATAL_ERROR_TYPE);
        }

        private function generate_error_page($title, $content, $type) {
            $this->error_page = new ErrorPage($title, $content, $this->get_page_params($type));
            $this->error = true;
            return $this->error_page;
        }

        private function get_page($request) {
            $params = $this->params;
            $file = $this->get_file_from_request($request);
            if ($file === false) return $this->generate_error_page('Page Not Found',
                'The Page you requested is yet to be made. Try again later.', ErrorPage::MISSING_PAGE_ERROR_TYPE);
            $params['request'] = $request;
            $params['file_uri'] = $file->value;
            if ($request !== 'index') $params['entry_page'] = $file->first_page;
            return MarkdownPage::fromFile($file, $params);
        }

        private function get_page_params($mode = '') {
            $params = array();
            $params['local_base'] = $this->local_base;

            if ($mode === '') $mode = $this->mode;
            $params['mode'] = $mode;
            switch ($mode) {
                case ErrorPage::FATAL_ERROR_TYPE:
                    $params['error_type'] = ErrorPage::FATAL_ERROR_TYPE;
                    break;

                case ErrorPage::NORMAL_ERROR_TYPE:
                case ErrorPage::MISSING_PAGE_ERROR_TYPE:
                    $params['error_type'] = $mode;
                    $params['index_key'] = 'index';
                    $params['docs_path'] = $this->docs_path;
                    $protocol = '//';
                    $params['base_url'] = $protocol . $this->base_url;
                    $params['base_page'] = $params['base_url'];
                    $params['host'] = $this->host;
                    $params['tree'] = $this->tree;
                    $params['index'] = ($this->tree->index_page !== false) ? $this->tree->index_page : $this->tree->first_page;
                    $params['clean_urls'] = $this->options['clean_urls'];

                    $params['tagline'] = $this->options['tagline'];
                    $params['title'] = $this->options['title'];
                    $params['author'] = $this->options['author'];
                    $params['image'] = $this->options['image'];
                    if ($params['image'] !== '') $params['image'] = str_replace('<base_url>', $params['base_url'], $params['image']);
                    $params['repo'] = $this->options['repo'];
                    $params['links'] = $this->options['links'];
                    $params['twitter'] = $this->options['twitter'];
                    $params['google_analytics'] = ($g = $this->options['google_analytics']) ?
                        DauxHelper::google_analytics($g, $this->host) : '';
                    $params['piwik_analytics'] = ($p = $this->options['piwik_analytics']) ?
                        DauxHelper::piwik_analytics($p, $this->options['piwik_analytics_id']) : '';

                    $params['template'] = $this->options['template'];
                    $params['theme'] = DauxHelper::configure_theme($this->local_base . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR .
                        $this->options['template'] . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . $this->options['theme'] . '.thm', $params['base_url'],
                        $this->local_base, $params['base_url'] . "templates/" . $params['template'] . "/themes/" . $this->options['theme'] . '/');
                    break;

                case Daux::LIVE_MODE:
                    $params['docs_path'] = $this->docs_path;
                    $params['index_key'] = 'index';
                    $protocol = '//';
                    $params['base_url'] = $protocol . $this->base_url;
                    $params['base_page'] = $params['base_url'];
                    $params['host'] = $this->host;
                    $params['tree'] = $this->tree;
                    $params['index'] = ($this->tree->index_page !== false) ? $this->tree->index_page : $this->tree->first_page;
                    $params['clean_urls'] = $this->options['clean_urls'];

                    $params['tagline'] = $this->options['tagline'];
                    $params['title'] = $this->options['title'];
                    $params['author'] = $this->options['author'];
                    $params['image'] = $this->options['image'];
                    if ($params['image'] !== '') $params['image'] = str_replace('<base_url>', $params['base_url'], $params['image']);
                    $params['repo'] = $this->options['repo'];
                    $params['links'] = $this->options['links'];
                    $params['twitter'] = $this->options['twitter'];
                    $params['google_analytics'] = ($g = $this->options['google_analytics']) ?
                        DauxHelper::google_analytics($g, $this->host) : '';
                    $params['piwik_analytics'] = ($p = $this->options['piwik_analytics']) ?
                        DauxHelper::piwik_analytics($p, $this->options['piwik_analytics_id']) : '';

                    $params['template'] = $this->options['template'];
                    $params['theme'] = DauxHelper::configure_theme($this->local_base . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR .
                        $this->options['template'] . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . $this->options['theme'] . '.thm', $params['base_url'],
                        $this->local_base, $params['base_url'] . "templates/" . $params['template'] . "/themes/" . $this->options['theme'] . '/', $mode);


                    if ($params['breadcrumbs'] = $this->options['breadcrumbs'])
                        $params['breadcrumb_separator'] = $this->options['breadcrumb_separator'];
                    $params['multilanguage'] = !empty($this->options['languages']);
                    $params['languages'] = $this->options['languages'];
                    if (empty($this->options['languages'])) {
                        $params['entry_page'] = $this->tree->first_page;
                    } else {
                        foreach ($this->options['languages'] as $key => $name) {
                            $params['entry_page'][$key] = $this->tree->value[$key]->first_page;
                        }
                    }

                    $params['toggle_code'] = $this->options['toggle_code'];
                    $params['float'] = $this->options['float'];
                    $params['date_modified'] = $this->options['date_modified'];
                    $params['file_editor'] = $this->options['file_editor'];
                    break;

                case Daux::STATIC_MODE:
                    $params['docs_path'] = $this->docs_path;
                    $params['index_key'] = 'index.html';
                    $params['base_url'] = '';
                    $params['base_page'] = $params['base_url'];
                    $params['tree'] = $this->tree;
                    $params['index'] = ($this->tree->index_page !== false) ? $this->tree->index_page : $this->tree->first_page;

                    $params['tagline'] = $this->options['tagline'];
                    $params['title'] = $this->options['title'];
                    $params['author'] = $this->options['author'];
                    $params['image'] = $this->options['image'];
                    $params['repo'] = $this->options['repo'];
                    $params['links'] = $this->options['links'];
                    $params['twitter'] = $this->options['twitter'];
                    $params['google_analytics'] = ($g = $this->options['google_analytics']) ?
                        DauxHelper::google_analytics($g, $this->host) : '';
                    $params['piwik_analytics'] = ($p = $this->options['piwik_analytics']) ?
                        DauxHelper::piwik_analytics($p, $this->options['piwik_analytics_id']) : '';

                    $params['template'] = $this->options['template'];
                    $params['theme'] = DauxHelper::configure_theme($this->local_base . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR .
                        $this->options['template'] . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . $this->options['theme'] . '.thm', $params['base_url'],
                        $this->local_base, $params['base_url'] . "templates/" . $params['template'] . "/themes/" . $this->options['theme'] . '/', $mode);

                    if ($params['breadcrumbs'] = $this->options['breadcrumbs'])
                        $params['breadcrumb_separator'] = $this->options['breadcrumb_separator'];
                    $params['multilanguage'] = !empty($this->options['languages']);
                    $params['languages'] = $this->options['languages'];
                    if (empty($this->options['languages'])) {
                        $params['entry_page'] = $this->tree->first_page;
                    } else {
                        foreach ($this->options['languages'] as $key => $name) {
                            $params['entry_page'][$key] = $this->tree->value[$key]->first_page;
                        }
                    }

                    $params['toggle_code'] = $this->options['toggle_code'];
                    $params['float'] = $this->options['float'];
                    $params['date_modified'] = $this->options['date_modified'];
                    $params['file_editor'] = false;
                    break;
            }
            return $params;
        }

        private function get_file_from_request($request) {
            $file = $this->tree->retrieve_file($request);
            return $file;
        }

    }

    ?>
