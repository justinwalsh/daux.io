<?php namespace Todaymade\Daux\Tree;

use Todaymade\Daux\DauxHelper;

    abstract class Entry
    {
        public $title;
        public $name;
        public $index_page;
        public $first_page;
        public $uri;
        public $local_path;
        public $last_modified;
        public $parents;

        function __construct($path = '', $parents = array()) {
            if (!isset($path) || $path == '' || !file_exists($path)) return;
            $this->local_path = $path;
            $this->parents = $parents;
            $this->last_modified = filemtime($path);
            $this->name = DauxHelper::pathinfo($path)['filename'];
            $this->title = $this->get_title_from_filename($this->name);
            $this->uri = $this->get_url_from_filename($this->getFilename($path));
            $this->index_page = false;
        }

        public function get_url() {
            $url = '';
            foreach ($this->parents as $node) {
                $url .= $node->uri . '/';
            }
            $url .=  $this->uri;
            return $url;
        }

        public function get_first_page() {
            foreach ($this->value as $node) {
                if ($node instanceof Content && $node->title != 'index')
                    return $node;
            }
            foreach ($this->value as $node) {
                if ($node instanceof Directory) {
                    $page = $node->get_first_page();
                    if ($page) return $page;
                }
            }
            return false;
        }

        public function write($content) {
            if (!is_writable($this->local_path)) {
                return false;
            }

            file_put_contents($this->local_path, $content);
            return true;
        }

        protected function getFilename($file) {
            $parts = explode('/', $file);
            return end($parts);
        }

        protected function get_title_from_filename($filename) {
            $filename = explode('_', $filename);
            if ($filename[0] == '' || is_numeric($filename[0])) unset($filename[0]);
            else {
                $t = $filename[0];
                if ($t[0] == '-') $filename[0] = substr($t, 1);
            }
            $filename = implode(' ', $filename);
            return $filename;
        }

        protected function get_url_from_filename($filename) {
            $filename = explode('_', $filename);
            if ($filename[0] == '' || is_numeric($filename[0])) unset($filename[0]);
            else {
                $t = $filename[0];
                if ($t[0] == '-') $filename[0] = substr($t, 1);
            }
            $filename = implode('_', $filename);
            return $filename;
        }

    }
