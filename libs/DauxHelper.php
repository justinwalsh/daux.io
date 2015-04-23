<?php namespace Todaymade\Daux;

class DauxHelper
{

    public static function getBreadcrumbFromRequest($request, $separator = 'Chevrons', $multilanguage = false)
    {
        if ($multilanguage) {
            $request = substr($request, strpos($request, '/') + 1);
        }
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

    public static function getTheme($theme_folder, $base_url, $local_base, $theme_url)
    {
        $name = static::pathinfo($theme_folder);

        $theme = array();
        if (is_file($theme_folder . DS . "config.json")) {
            $theme = json_decode(file_get_contents($theme_folder . DS . "config.json"), true);
            if (!$theme) {
                $theme = array();
            }
        }
        $theme['name'] = $name['filename'];

        //Default parameters for theme
        $theme += [
            'css' => [],
            'js' => [],
            'fonts' => [],
            'require-jquery' => false,
            'bootstrap-js' => false,
            'favicon' => '<base_url>resources/img/favicon.png',
            'templates' => $theme_folder . DS . 'templates',
        ];

        $substitutions = ['<local_base>' => $local_base, '<base_url>' => $base_url, '<theme_url>' => $theme_url];

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
}
