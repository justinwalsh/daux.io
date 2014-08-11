<?php
    namespace Todaymade\Daux;

    class DauxHelper {

        public static function get_breadcrumb_title_from_request($request, $separator = 'Chevrons', $multilanguage = false) {
            if ($multilanguage) $request = substr($request, strpos($request, '/') + 1);
            $request = str_replace('_', ' ', $request);
            switch ($separator) {
                case 'Chevrons':
                    $request = str_replace('/', ' <i class="glyphicon glyphicon-chevron-right"></i> ', $request);
                    return $request;
                case 'Colons':
                    $request = str_replace('/', ': ', $request);
                    return $request;
                case 'Spaces':
                    $request = str_replace('/', ' ', $request);
                    return $request;
                default:
                    $request = str_replace('/', $separator, $request);
                    return $request;
            }
            return $request;
        }

        public static function get_title_from_file($file) {
            $file = static::pathinfo($file);
            return static::get_title_from_filename($file['filename']);
        }

        public static function get_title_from_filename($filename) {
            $filename = explode('_', $filename);
            if ($filename[0] == '' || is_numeric($filename[0])) unset($filename[0]);
            $filename = implode(' ', $filename);

            return $filename;
        }

        public static function get_url_from_file($file) {
            $file = static::pathinfo($file);
            return static::get_url_from_filename($file['filename']);
        }

        public static function get_url_from_filename($filename) {
            $filename = explode('_', $filename);
            if ($filename[0] == '' || is_numeric($filename[0])) unset($filename[0]);
            $filename = implode('_', $filename);
            return $filename;
        }

        public static function build_directory_tree($dir, $ignore, $mode) {
            return static::directory_tree_builder($dir, $ignore, $mode);
        }

        public static function get_request_from_url($url, $base_url) {
            $url = substr($url, strlen($base_url) + 1);
            if (strpos($url, 'index.php') === 0) {
                $request = (($i = strpos($url, 'request=')) !== false) ? $request = substr($url, $i + 8) : '';
                if ($end = strpos($request, '&')) $request = substr($request, 0, $end);
                $request = ($request === '') ? 'index' : $request;
            } else {
                $request = ($url == '') ? 'index' : $url;
                $request = ($end = strpos($request, '?')) ? substr($request, 0, $end) : $request;
            }
            return $request;
        }

        public static function configure_theme($theme, $base_url, $local_base, $mode = Daux::LIVE_MODE) {
            if (is_file($theme)) {
                $theme = file_get_contents($theme);
                $theme = json_decode($theme, true);
                if (!$theme) $theme = array();
            } else $theme = array();

            if ($mode === Daux::LIVE_MODE) {
                if (!isset($theme['favicon'])) $theme['favicon'] = utf8_encode($base_url . 'img/favicon.png');
                else {
                    $theme['favicon'] = utf8_encode(str_replace('<base_url>', $base_url, $theme['favicon']));
                }

                if (!isset($theme['css'])) $theme['css'] = array();
                else {
                    foreach ($theme['css'] as $key => $css) {
                        $theme['css'][$key] = utf8_encode(str_replace('<base_url>', $base_url, $css));
                    }
                }
                if (!isset($theme['fonts'])) $theme['fonts'] = array();
                else {
                    foreach ($theme['fonts'] as $key => $font) {
                        $theme['fonts'][$key] = utf8_encode(str_replace('<base_url>', $base_url, $font));
                    }
                }
                if (!isset($theme['js'])) $theme['js'] = array();
                else {
                    foreach ($theme['js'] as $key => $js) {
                        $theme['js'][$key] = utf8_encode(str_replace('<base_url>', $base_url, $js));
                    }
                }
            } else {
                if (!isset($theme['favicon'])) $theme['favicon'] = '<base_url>img/favicon.png';
                if (!isset($theme['css'])) $theme['css'] = array();
                if (!isset($theme['fonts'])) $theme['fonts'] = array();
                if (!isset($theme['js'])) $theme['js'] = array();
            }

            if (!isset($theme['template'])) $theme['template'] = $local_base . DIRECTORY_SEPARATOR . 'templates' .
                DIRECTORY_SEPARATOR . 'default.tpl';
            else $theme['template'] = str_replace('<local_base>', $local_base, $theme['template']);
            if (!isset($theme['error-template'])) $theme['error-template'] = $local_base . DIRECTORY_SEPARATOR . 'templates' .
                DIRECTORY_SEPARATOR . 'error.tpl';
            else $theme['error-template'] = str_replace('<local_base>', $local_base, $theme['error-template']);
            if (!isset($theme['require-jquery'])) $theme['require-jquery'] = false;
            if (!isset($theme['bootstrap-js'])) $theme['bootstrap-js'] = false;
            return $theme;
        }

        public static function rebase_theme($theme, $base_url) {
            $theme['favicon'] = utf8_encode(str_replace('<base_url>', $base_url, $theme['favicon']));
            foreach ($theme['css'] as $key => $css) {
                $theme['css'][$key] = utf8_encode(str_replace('<base_url>', $base_url, $css));
            }
            foreach ($theme['fonts'] as $key => $font) {
                $theme['fonts'][$key] = utf8_encode(str_replace('<base_url>', $base_url, $font));
            }
            foreach ($theme['js'] as $key => $js) {
                $theme['js'][$key] = utf8_encode(str_replace('<base_url>', $base_url, $js));
            }
            return $theme;
        }

        public static function google_analytics($analytics, $host) {
            $ga = <<<EOT
            <script>
                (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
                    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
EOT;
            $ga .= "ga('create', '" . $analytics . "', '" . $host . "');";
            $ga .= "ga('send', 'pageview');";
            $ga .= '</script>';
            return $ga;
        }

        public static function piwik_analytics($analytics_url, $analytics_id) {
            $pa = <<<EOT
                <script type="text/javascript">
                var _paq = _paq || [];
                _paq.push(["trackPageView"]);
                _paq.push(["enableLinkTracking"]);
                (function() {
EOT;
            $pa .= 'var u=(("https:" == document.location.protocol) ? "https" : "http") + "://' . $analytics_url . '/";';
            $pa .= '_paq.push(["setTrackerUrl", u+"piwik.php"]);';
            $pa .= '_paq.push(["setSiteId", ' . $analytics_id . ']);';
            $pa .= <<<EOT
                var d=document, g=d.createElement("script"), s=d.getElementsByTagName("script")[0]; g.type="text/javascript";
                g.defer=true; g.async=true; g.src=u+"piwik.js"; s.parentNode.insertBefore(g,s);
                })();
                </script>
EOT;
            return $pa;
        }

        private static function directory_tree_builder($dir, $ignore, $mode = Daux::LIVE_MODE, $parents = null) {
            if ($dh = opendir($dir)) {
                $node = new Directory_Entry($dir, $parents);
                $new_parents = $parents;
                if (is_null($new_parents)) $new_parents = array();
                else $new_parents[] = $node;
                while (($entry = readdir($dh)) !== false) {
                    if ($entry == '.' || $entry == '..') continue;
                    $path = $dir . DIRECTORY_SEPARATOR . $entry;
                    if (is_dir($path) && in_array($entry, $ignore['folders'])) continue;
                    if (!is_dir($path) && in_array($entry, $ignore['files'])) continue;

                    $file_details = static::pathinfo($path);
                    if (is_dir($path)) $entry = static::directory_tree_builder($path, $ignore, $mode, $new_parents);
                    else if (in_array($file_details['extension'], Daux::$VALID_MARKDOWN_EXTENSIONS))
                    {
                        $entry = new Directory_Entry($path, $new_parents);
                        if ($mode === Daux::STATIC_MODE) $entry->uri .= '.html';
                    }
                    if ($entry instanceof Directory_Entry) $node->value[$entry->uri] = $entry; 
                }
                $node->sort();
                $node->first_page = $node->get_first_page();
                $index_key = ($mode === Daux::LIVE_MODE) ? 'index' : 'index.html';
                if (isset($node->value[$index_key])) {
                    $node->value[$index_key]->first_page = $node->first_page;
                    $node->index_page =  $node->value[$index_key];
                } else $node->index_page = false;
                return $node;
            }
        }

        public static function pathinfo($path) {
            preg_match('%^(.*?)[\\\\/]*(([^/\\\\]*?)(\.([^\.\\\\/]+?)|))[\\\\/\.]*$%im', $path, $m);
            if (isset($m[1])) $ret['dir']=$m[1];
            if (isset($m[2])) $ret['basename']=$m[2];
            if (isset($m[5])) $ret['extension']=$m[5];
            if (isset($m[3])) $ret['filename']=$m[3];
            return $ret;
        }

        public static function clean_copy_assets($path, $local_base){
            @mkdir($path);
            static::clean_directory($path);

            @mkdir($path . DIRECTORY_SEPARATOR . 'img');
            static::copy_recursive($local_base . DIRECTORY_SEPARATOR . 'img', $path . DIRECTORY_SEPARATOR . 'img');
            @mkdir($path . DIRECTORY_SEPARATOR . 'js');
            static::copy_recursive($local_base . DIRECTORY_SEPARATOR . 'js', $path . DIRECTORY_SEPARATOR . 'js');
            @mkdir($path . DIRECTORY_SEPARATOR . 'themes');
            static::copy_recursive($local_base . DIRECTORY_SEPARATOR . 'themes', $path . DIRECTORY_SEPARATOR . 'themes');
        }

        //  Rmdir
        private static function clean_directory($dir) {
            $it = new \RecursiveDirectoryIterator($dir);
            $files = new \RecursiveIteratorIterator($it,
                \RecursiveIteratorIterator::CHILD_FIRST);
            foreach($files as $file) {
                if ($file->getFilename() === '.' || $file->getFilename() === '..') continue;
                if ($file->isDir()) rmdir($file->getRealPath());
                else unlink($file->getRealPath());
            }
        }

        private static function copy_recursive($src,$dst) {
            $dir = opendir($src);
            @mkdir($dst);
            while(false !== ( $file = readdir($dir)) ) {
                if (( $file != '.' ) && ( $file != '..' )) {
                    if ( is_dir($src . '/' . $file) ) {
                        static::copy_recursive($src . '/' . $file,$dst . '/' . $file);
                    }
                    else {
                        copy($src . '/' . $file,$dst . '/' . $file);
                    }
                }
            }
            closedir($dir);
        }

    }
?>