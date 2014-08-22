<?php
    namespace Todaymade\Daux;
    class Directory_Entry
    {
        const FILE_TYPE = 'FILE_TYPE';
        const DIRECTORY_TYPE = 'DIRECTORY_TYPE';
        public $name;
        public $title;
        public $type;
        public $index_page;
        public $first_page;
        public $value;
        public $uri;
        public $local_path;
        public $last_modified;
        public $parents;

        function __construct($path = '', $parents = array()) {
            if (!isset($path) || $path == '' || !file_exists($path)) return;
            $this->local_path = $path;
            $this->parents = $parents;
            $this->last_modified = filemtime($path);
            $this->name = DauxHelper::pathinfo($path);
            $this->name = $this->name['filename'];
            $this->title = DauxHelper::get_title_from_file($this->name);
            $this->uri = DauxHelper::get_url_from_filename($this->name);
            $this->index_page = false;
            if (is_dir($path)) {
                $this->type = Directory_Entry::DIRECTORY_TYPE;
                $this->value = array();
            } else {
                $this->type = Directory_Entry::FILE_TYPE;
                $this->value = $this->uri;
            }
        }

        public function sort() {
            if ($this->type == static::DIRECTORY_TYPE) uasort($this->value, array($this, 'compare_directory_entries'));
        }

        public function retrieve_file($request, $get_first_file = false) {
            $tree = $this;
            $request = explode('/', $request);
            foreach ($request as $node) {
                if ($tree->type === static::DIRECTORY_TYPE) {
                    if (isset($tree->value[$node])) $tree = $tree->value[$node];
                    else {
                        if ($node === 'index' || $node === 'index.html') {
                            if ($get_first_file) {
                                return ($tree->index_page) ? $tree->index_page : $tree->first_page;
                            } else {
                                return $tree->index_page;
                            }
                        } else return false;
                    }
                } else return false;
            }
            if ($tree->type === static::DIRECTORY_TYPE) {
                if ($tree->index_page) return $tree->index_page;
                else return ($get_first_file) ? $tree->first_page : false;
            } else {
                return $tree;
            }
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
                if ($node->type === static::FILE_TYPE && $node->title != 'index')
                    return $node;
            }
            foreach ($this->value as $node) {
                if ($node->type === static::DIRECTORY_TYPE) {
                    $page = $node->get_first_page();
                    if ($page) return $page;
                }
            }
            return false;
        }

        public function write($content) {
            if (is_writable($this->local_path)) file_put_contents($this->local_path, $content);
            else return false;
            return true;
        }

        private function compare_directory_entries($a, $b) {
            $name_a = explode('_', $a->name);
            $name_b = explode('_', $b->name);
            if (is_numeric($name_a[0])) {
                $a = intval($name_a[0]);
                if (is_numeric($name_b[0])) {
                    $b = intval($name_b[0]);
                    if (($a >= 0) == ($b >= 0)) {
                        $a = abs($a);
                        $b = abs($b);
                        if ($a == $b) return (strcasecmp($name_a[1], $name_b[1]));
                        return ($a > $b) ? 1 : -1;
                    }
                    return ($a >= 0) ? -1 : 1;
                }
                $t = $name_b[0];
                if ($t && $t[0] === '-') return -1;
                return ($a < 0) ? 1 : -1;
            } else {
                if (is_numeric($name_b[0])) {
                    $b = intval($name_b[0]);
                    if ($b >= 0) return 1;
                    $t = $name_a[0];
                    if ($t && $t[0] === '-') return 1;
                    return ($b >= 0) ? 1 : -1;
                }
                $p = $name_a[0];
                $q = $name_b[0];
                if (($p && $p[0] === '-') == ($q && $q[0] === '-')) return strcasecmp($p, $q);
                else return ($p[0] === '-') ? 1 : -1;
            }
        }
    }
?>