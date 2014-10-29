<?php
    namespace Todaymade\Daux;

    interface Page
    {
        function get_page_content();
        function display();
    }

    class SimplePage implements Page
    {
        protected $title;
        protected $content;
        protected $html = null;

        public function __construct($title, $content) {
            $this->initialize_page($title, $content);
        }

        public function initialize_page($title, $content) {
            $this->title = $title;
            $this->content = $content;
        }

        public function  display() {
            header('Content-type: text/html; charset=utf-8');
            echo $this->get_page_content();
        }

        public function get_page_content() {
            if (is_null($this->html)) {
                $this->html = $this->generate_page();
            }

            return $this->html;
        }

        private function generate_page() {
            return $this->content;
        }
    }

    class ErrorPage extends SimplePage
    {
        const NORMAL_ERROR_TYPE = 'NORMAL_ERROR';
        const MISSING_PAGE_ERROR_TYPE = 'MISSING_PAGE_ERROR';
        const FATAL_ERROR_TYPE = 'FATAL_ERROR';

        private $params;
        private $type;
        private static $template;

        public function __construct($title, $content, $params) {
            parent::__construct($title, $content);
            $this->params = $params;
            $this->type = $params['error_type'];
        }

        public function display() {
            http_response_code($this->type === static::MISSING_PAGE_ERROR_TYPE ? 404 : 500);
            parent::display();
        }

        public function get_page_content() {
            if ($this->type !== static::FATAL_ERROR_TYPE && is_null(static::$template)) {
                include_once($this->params['theme']['error-template']);
                static::$template = new Template();
            }

            if (is_null($this->html)) {
                $this->html = $this->generate_page();
            }

            return $this->html;
        }

        public function generate_page() {
            if ($this->type === static::FATAL_ERROR_TYPE) return $this->content;
            $params = $this->params;
            $page['title'] = $this->title;
            $page['theme'] = $params['theme'];
            $page['content'] = $this->content;
            $page['google_analytics'] = $params['google_analytics'];
            $page['piwik_analytics'] = $params['piwik_analytics'];

            return static::$template->get_content($page, $params);
        }
    }

    class MarkdownPage extends  SimplePage
    {
        private $filename;
        private  $params;
        private $language;
        private $mtime;
        private $homepage;
        private $breadcrumb_trail;
        private static $template;

        public function __construct() {

        }

        // For Future Expansion
        public static function fromFile($file, $params) {
            $instance = new self();
            $instance->initialize_from_file($file, $params);
            return $instance;
        }

        private function initialize_from_file($file, $params) {
            $this->title = $file->title;
            $this->filename = $file->name;
            $this->path = $file->local_path;
            $this->mtime = $file->last_modified;
            $this->params = $params;

            if ($this->title === 'index') {
                $this->homepage = ($this->filename === '_index');
                $minimum_parent_dir_size = ($params['multilanguage']) ? 2 : 1;
                if (count($file->parents) >= $minimum_parent_dir_size) {
                    $parent = end($file->parents);
                    $this->title = $parent->title;
                } else $this->title = $params['title'];
            } else {
                $this->homepage = false;
            }
            if ($params['breadcrumbs'])
                $this->breadcrumb_trail = $this->build_breadcrumb_trail($file->parents, $params['multilanguage']);
            $this->language = '';
            if ($params['multilanguage'] && !empty($file->parents)) {
                reset($file->parents);
                $language_dir = current($file->parents);
                $this->language = $language_dir->name;
            }
            if (is_null(static::$template)) {
                include_once($params['theme']['template']);
                static::$template = new Template();
            }
        }

        private function build_breadcrumb_trail($parents, $multilanguage) {
            if ($multilanguage && !empty($parents)) $parents = array_splice($parents, 1);
            $breadcrumb_trail = array();
            if (!empty($parents)) {
                foreach ($parents as $node) {
                    $breadcrumb_trail[$node->title] = $node->get_url();
                }
            }
            return $breadcrumb_trail;
        }

        public function get_page_content() {
            if (is_null($this->html)) {
                $this->content = file_get_contents($this->path);
                $this->html = $this->generate_page();
            }

            return $this->html;
        }

        private function generate_page() {
            $params = $this->params;
            $Parsedown = new \Parsedown();
            if ($params['request'] === $params['index_key']) {
                if ($params['multilanguage']) {
                    foreach ($params['languages'] as $key => $name) {
                        $entry_page[utf8_encode($name)] = utf8_encode($params['base_page'] . $params['entry_page'][$key]->get_url());
                    }
                } else $entry_page['View Documentation'] = utf8_encode($params['base_page'] . $params['entry_page']->uri);
            } else if ($params['file_uri'] === 'index')
                $entry_page[utf8_encode($params['entry_page']->title)] = utf8_encode($params['base_page'].
                    $params['entry_page']->get_url());
            $page['entry_page'] = (isset($entry_page)) ? $entry_page : null;

            $page['homepage'] = $this->homepage;
            $page['title'] = $this->title;
            $page['tagline'] = $params['tagline'];
            $page['author'] = $params['author'];
            $page['filename'] = $this->filename;
            if ($page['breadcrumbs'] = $params['breadcrumbs']) {
                $page['breadcrumb_trail'] = $this->breadcrumb_trail;
                $page['breadcrumb_separator'] = $params['breadcrumb_separator'];
            }
            $page['language'] = $this->language;
            $page['path'] = $this->path;
            $page['request'] = utf8_encode($params['request']);
            $page['theme'] = $params['theme'];
            $page['modified_time'] = filemtime($this->path);
            $page['markdown'] = $this->content;
            $page['content'] = $Parsedown->text($this->content);
            $page['file_editor'] = $params['file_editor'];
            $page['google_analytics'] = $params['google_analytics'];
            $page['piwik_analytics'] = $params['piwik_analytics'];

            return static::$template->get_content($page, $params);
        }
    }

    ?>