<?php
    require_once(dirname(__FILE__) . "/../vendor/autoload.php");

    $tree = array();
    $base = dirname(dirname(__FILE__));
    $options = get_options(isset($argv[2]) ? $argv[2] : '');
    $docs_path = $base . '/' . $options['docs_path'];
    $multilanguage = !empty($options['languages']) ? TRUE : FALSE;

    //  Options
    function get_options($config_file) {
        global $base;
        $options = array(
            'title' => "Documentation",
            'tagline' => false,
            'image' => false,
            'theme' => 'red',
            'docs_path' => 'docs',
            'date_modified' => true,
            'float' => true,
            'repo' => false,
            'toggle_code' => false,
            'twitter' => array(),
            'links' => array(),
            'colors' => false,
            'clean_urls' => true,
            'google_analytics' => false,
            'piwik_analytics' => false,
            'piwik_analytics_id' => 1,
            'ignore' => array(),
            'languages' => array(),
            'file_editor' => false,
            'template' => 'default'
        );

        // Load User Config
        $config_file = (($config_file === '') ? 'docs/config.json' : $config_file);
        if (substr($config_file, 0, 1) !== '/') $config_file = $base . '/' . $config_file;
        if (file_exists($config_file)) {
            $config = json_decode(file_get_contents($config_file), true);
            if(!isset($config)) {
                echo '<strong>Daux.io Config Error:</strong><br> The config file ' . $config_file . ' that was passed contained invalid JSON. <a href="http://daux.io">Learn More</a>.';
                exit;
            }
            $options = array_merge($options, $config);
        }
        if (!isset($options['ignore']['files'])) $options['ignore']['files'] = array();
        if (!isset($options['ignore']['folders'])) $options['ignore']['folders'] = array();

        if ($options['theme'] !== 'custom') {
            $themes = array("blue","navy","green","red");
            if (!in_array($options['theme'], $themes)) {
                echo "<strong>Daux.io Config Error:</strong><br>The theme you set is not not a valid option. Please use one of the following options: " . join(array_keys($themes), ', ') . ' or <a href="http://daux.io">learn more</a> about how to customize the colors.';
                exit;
            }
        } else {
            if (empty($options['colors'])) {
                echo '<strong>Daux.io Config Error:</strong><br>You are trying to use a custom theme, but did not setup your color options in the config. <a href="http://daux.io">Learn more</a> about how to customize the colors.';
                exit;
            }
        }
        if (!ini_get('date.timezone')) date_default_timezone_set('GMT');
        return $options;
    }

    //  Build Directory Tree
    function build_tree() {
        global $tree, $options, $docs_path , $multilanguage, $output_language;
        if (!$multilanguage) $tree = directory_tree($docs_path, $options['ignore']);
        else
            foreach ($options['languages'] as $languageKey => $language) {
                $output_language = $languageKey;
                $tree[$languageKey] = directory_tree($docs_path . '/' . $languageKey, $options['ignore']);
            }
    }

    //  Recursively add files & directories to Tree
    function directory_tree($dir, $ignore) {
        global $base_doc, $multilanguage, $output_language;
        $tree = array();
        $Item = array_diff(scandir($dir), array(".", ".."));
        foreach ($Item as $key => $value) {
            if (is_dir($dir . '/' . $value)) {
                if (!in_array($value, $ignore['folders']))
                    $tree[$value] = directory_tree($dir . '/' . $value, $ignore);
            } else if (!in_array($value, $ignore['files'])) {
                    if (substr($value, -3) === ".md") {
                        $tree[$value] = $value;
                        if ($multilanguage)
                            $base_doc[$output_language] = isset($base_doc[$output_language]) ? $base_doc[$output_language] : $dir . '/' . $value;
                        else $base_doc = isset($base_doc) ? $base_doc : $dir . '/' . $value;
                    }
                }
        }
        return $tree;
    }

    //  Build Navigation
    function get_navigation($url) {
        global $tree, $multilanguage, $output_language, $output_path;
        $dir = isset($output_path) ? $output_path : '';
        $return = "<ul class=\"nav nav-list\">";
        $return .= $multilanguage ? build_navigation($tree[$output_language], (($dir !== '') ? $dir . '/' : '') . $output_language, $url) : build_navigation($tree, $dir, $url);
        $return .= "</ul>";
        return $return;
    }

    function build_navigation($tree, $current_dir, $url) {
        global $mode, $base_path, $docs_path, $options;
        $return = "";
        if ($mode === 'Static') $t = relative_path($current_dir . "/.", $url) . '/';
        else {
            $t = "http://" . $base_path . '/';
            if (!$options['clean_urls']) $t .= 'index.php?';
            $rel = clean_url($current_dir, 'Live');
            $t .= ($rel === '') ? '' : $rel . '/';
        }
        foreach ($tree as $key => $node)
            if (is_array($node)) {
                $return .= "<li";
                if (!(strpos($url, $key) === FALSE)) $return .= " class=\"open\"";
                $return .= ">";
                $link = "#";
                $nav_class = "aj-nav ";
                if(in_array("index.md", $node)) {
                    $link = $t . clean_url($key, $mode);
                    $nav_class = "";
                }
                $return .= "<a href=\"" . $link . "\" class=\"" . $nav_class . "folder\">";
                $return .= clean_url($key, "Title");
                $return .= "</a>";
                $return .= "<ul class=\"nav nav-list\">";
                $dir = ($current_dir === '') ? $key : $current_dir . '/' . $key;
                $return .= build_navigation($node, $dir, $url);
                $return .= "</ul>";
                $return .= "</li>";
            }
            else if($node !== "index.md") {
                $return .= "<li";
                if ($url === $current_dir . '/' . $node) $return .= " class=\"active\"";
                $return .= ">";
                $link = $t . clean_url($node, $mode);
                $return .= "<a href=\"" . $link . "\">" . clean_url($node, "Title");
                $return .= "</a></li>";
            }
        return $return;
    }

    //  Generate Documentation from Markdown file
    function generate_page($file) {
        global $base, $base_doc, $base_path, $docs_path, $options, $mode, $relative_base;
        $template = $options['template'];
        $file_relative_path = str_replace($docs_path . '/', "", $file);
        if ($file_relative_path === 'index.md') $homepage = TRUE;
        else $homepage = FALSE;
        if (!$file) {
            $page['path'] = '';
            $page['markdown'] = '';
            $page['title'] = 'Oh No';
            $page['content'] = "<h3>Oh No. That page doesn't exist</h3>";
            $options['file_editor'] = false;
        } else {
            $page['path'] = $file_relative_path;
            $page['markdown'] = file_get_contents($file);
            $page['modified'] = filemtime($file);

            $Parsedown = new Parsedown();

            $page['content'] =  $Parsedown->text($page['markdown']);
            $page['title'] = clean_url($file, 'Title');
        }
        $relative_base = ($mode === 'Static') ? relative_path("", $file) : "http://" . $base_path . '/';
        ob_start();
        include($base . "/template/" . $template . ".tpl");
        $return = ob_get_contents();
        @ob_end_clean();
        return $return;
    }

    //  File to URL
    function clean_url($url, $mode = 'Static') {
        global $docs_path, $output_path, $options;
        switch ($mode) {
            case 'Live':
                $url = str_replace(array(".md", ".html", ".php"), "", $url);
            case 'Static':
                $url = str_replace(".md", ".html", $url);
                $remove = array($docs_path . '/');
                if (isset($output_path)) $remove[] = $output_path . '/';
                $url = str_replace($remove, "", $url);
                $url = explode('/', $url);
                foreach ($url as &$a) {
                    $a = explode('_', $a);
                    if (isset($a[0]) && is_numeric($a[0])) unset($a[0]);
                    $a = implode('_', $a);
                }
                $url = implode('/', $url);
                return $url;
            case 'Title':
            case 'Filename':
                $parts = array_reverse(explode('/', $url));
                if (isset($parts[0])) {
                    if ($parts[0] === "index.md" && isset($parts[1])) $url = $parts[1];
                    else $url = $parts[0];
                }
                $url = explode('_', $url);
                if (isset($url[0]) && is_numeric($url[0])) unset($url[0]);
                if ($mode === 'Filename') $url = implode('_', $url);
                else $url = implode(' ', $url);
                $url = str_replace(array(".md", ".html"), "", $url);
                return $url;

        }
    }

    //  Get Path based on Server. For Use in template file.
    function get_url($url) {
        global $mode, $options, $relative_base;
        $t = clean_url($url, $mode);
        if ($t === 'index') {
            if ($mode === 'Static') return $relative_base . 'index.html';
            else return $relative_base;
        }
        if ($mode === 'Live' && !$options['clean_urls']) $t = 'index.php?' . $t;
        return $t;
    }

    //  Relative Path From Path2 to Path1
    function relative_path($path1, $path2) {
        global $output_path, $docs_path, $base;
        $remove = array($docs_path . '/');
        if (isset($output_path)) $remove[] = $output_path . '/';
        $remove[] = $base . '/';
        $path1 = str_replace($remove, "", $path1);
        $path2 = str_replace($remove, "", $path2);
        $nesting = substr_count($path2, "/");
        if ($nesting == 0) return clean_url($path1);
        $return = "";
        $t = 0;
        while ($t < $nesting) {
            $return .= "../";
            $t += 1;
        }
        $return .= clean_url($path1);
        return $return;
    }


?>
