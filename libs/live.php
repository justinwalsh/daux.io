<?php
    //  Generate Page
    function generate_live($page) {
        global $options, $multilanguage, $output_language, $base, $base_path, $mode;
        $mode = 'Live';
        if ($multilanguage) {
            $b = explode('/', clean_url($page, "Live"));
            $output_language = $b[0];
        }
        $file = clean_url_to_file($page);
        if (!is_file($file)) $file = FALSE;
        return generate_page($file);
    }

    //  Clean Live Url
    function clean_live($url) {
        return clean_url($url, "Live");
    }

    //  Retrieve File From Clean URL
    function clean_url_to_file($url) {
        global $tree, $docs_path;
        $location = getfile($tree, $url, $docs_path);
        return $location;
    }

    //  Get File from $url
    function getfile($tree, $url, $current_dir, $flag = FALSE) {
        global $docs_path, $base_doc, $options;
        $url = clean_url($url, "Live");
        if ($url === '' || $url === 'index') {
            if (is_file($docs_path . "/index.md")) return $docs_path . "/index.md";
            else {
                if (empty($options['languages'])) return $base_doc;
                else {
                	$t = array_keys($base_doc);
                	return $base_doc[$t[0]];
                }
            }
        } else {
            $url = explode("/", $url);
            $file = $docs_path;
            foreach ($url as $part) {
                if (isset($tree)) {
                    $dirs = array_keys($tree);
                    $key = array_search($part, array_map("clean_live", $dirs));
                } else $key = FALSE;
                if ($key === FALSE) {
                    return FALSE;
                }
                $file .= '/' . $dirs[$key];
                $tree = $tree[$dirs[$key]];
            }
            return $file;
        }
    }

?>