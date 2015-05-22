<?php namespace Todaymade\Daux;

use Todaymade\Daux\Tree\Directory;

class DauxHelper
{
    public static function getTheme($theme_name, $base_url, $local_base, $current_url)
    {
        $theme_folder = $local_base . DS . 'resources' . DS . 'themes' . DS . $theme_name;
        $theme_url = $base_url . "resources/themes/" . $theme_name . '/';

        $theme = array();
        if (is_file($theme_folder . DS . "config.json")) {
            $theme = json_decode(file_get_contents($theme_folder . DS . "config.json"), true);
            if (!$theme) {
                $theme = array();
            }
        }

        //Default parameters for theme
        $theme += [
            'name' => $theme_name,
            'css' => [],
            'js' => [],
            'fonts' => [],
            'require-jquery' => false,
            'bootstrap-js' => false,
            'favicon' => '<base_url>resources/img/favicon.png',
            'templates' => $theme_folder . DS . 'templates',
        ];

        $substitutions = ['<local_base>' => $local_base, '<base_url>' => $current_url, '<theme_url>' => $theme_url];

        // Substitute some placeholders
        $theme['templates'] = strtr($theme['templates'], $substitutions);
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

    public static function getCleanPath($path)
    {
        $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
        $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
        $absolutes = array();
        foreach ($parts as $part) {
            if ('.' == $part) {
                continue;
            }
            if ('..' == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }
        return implode(DIRECTORY_SEPARATOR, $absolutes);
    }

    public static function pathinfo($path)
    {
        preg_match('%^(.*?)[\\\\/]*(([^/\\\\]*?)(\.([^\.\\\\/]+?)|))[\\\\/\.]*$%im', $path, $m);
        if (isset($m[1])) {
            $ret['dir']=$m[1];
        }
        if (isset($m[2])) {
            $ret['basename']=$m[2];
        }
        if (isset($m[5])) {
            $ret['extension']=$m[5];
        }
        if (isset($m[3])) {
            $ret['filename']=$m[3];
        }
        return $ret;
    }

    public static function getFile($tree, $request)
    {
        $request = explode('/', $request);
        foreach ($request as $node) {
            // If the element we're in currently is not a
            // directory, we failed to find the requested file
            if (!$tree instanceof Directory) {
                return false;
            }

            // if the node exists in the current request tree,
            // change the $tree variable to reference the new
            // node and proceed to the next url part
            if (isset($tree->value[$node])) {
                $tree = $tree->value[$node];
                continue;
            }

            // At this stage, we're in a directory, but no
            // sub-item matches, so the current node must
            // be an index page or we failed
            if ($node !== 'index' && $node !== 'index.html') {
                return false;
            }

            return $tree->getIndexPage();
        }

        // If the entry we found is not a directory, we're done
        if (!$tree instanceof Directory) {
            return $tree;
        }

        if ($tree->getIndexPage()) {
            return $tree->getIndexPage();
        }

        return false;
    }
}
