<?php namespace Todaymade\Daux;

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
            else {
                $t = $filename[0];
                if ($t[0] == '-') $filename[0] = substr($t, 1);
            }
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
            else {
                $t = $filename[0];
                if ($t[0] == '-') $filename[0] = substr($t, 1);
            }
            $filename = implode('_', $filename);
            return $filename;
        }

        public static function get_theme($theme_folder, $base_url, $local_base, $theme_url) {
            $name = static::pathinfo($theme_folder);

            $theme = array();
            if (is_file($theme_folder . DIRECTORY_SEPARATOR . "config.json")) {
                $theme = json_decode(file_get_contents($theme_folder . DIRECTORY_SEPARATOR . "config.json"), true);
                if (!$theme) $theme = array();
            }
            $theme['name'] = $name['filename'];

            //Default parameters for theme
            $theme += [
                'css' => [],
                'js' => [],
                'fonts' => [],
                'require-jquery' => false,
                'bootstrap-js' => false,
                'favicon' => '<base_url>img/favicon.png',
                'template' => $local_base . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'default/default.tpl',
                'error-template' => $local_base . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'default/error.tpl',
            ];

            $substitutions = ['<local_base>' => $local_base, '<base_url>' => $base_url, '<theme_url>' => $theme_url];

            // Substitute some placeholders
            $theme['template'] = strtr($theme['template'], $substitutions);
            $theme['error-template'] = strtr($theme['error-template'], $substitutions);
            $theme['favicon'] = utf8_encode(strtr($theme['favicon'], $substitutions));

            foreach ($theme['css'] as $key => $css) {
                $theme['css'][$key] = utf8_encode(strtr($css, $substitutions));
            }

            foreach ($theme['fonts'] as $key => $font) {
                $theme['fonts'][$key] = utf8_encode(strtr($font, $substitutions));
            }

            foreach ($theme['js'] as $key => $js) {
                $theme['js'][$key] = utf8_encode(strtr($js, $substitutions));
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

        public static function build_directory_tree($dir, $ignore, $mode = Daux::LIVE_MODE, $parents = null) {
            if ($dh = opendir($dir)) {
                $node = new Entry($dir, $parents);
                $new_parents = $parents;
                if (is_null($new_parents)) $new_parents = array();
                else $new_parents[] = $node;
                while (($entry = readdir($dh)) !== false) {
                    if ($entry == '.' || $entry == '..') continue;
                    $path = $dir . DIRECTORY_SEPARATOR . $entry;
                    if (is_dir($path) && in_array($entry, $ignore['folders'])) continue;
                    if (!is_dir($path) && in_array($entry, $ignore['files'])) continue;

                    $file_details = static::pathinfo($path);
                    if (is_dir($path)) $entry = static::build_directory_tree($path, $ignore, $mode, $new_parents);
                    else if (in_array($file_details['extension'], Daux::$VALID_MARKDOWN_EXTENSIONS))
                    {
                        $entry = new Entry($path, $new_parents);
                        if ($mode === Daux::STATIC_MODE) $entry->uri .= '.html';
                    }
                    if ($entry instanceof Entry) $node->value[$entry->uri] = $entry;
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
    }
