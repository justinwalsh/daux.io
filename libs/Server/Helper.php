<?php namespace Todaymade\Daux\Server;

use Todaymade\Daux\Daux;
use Todaymade\Daux\DauxHelper;

class Helper {
    public static function configure_theme($theme_file, $base_url, $local_base, $theme_url) {

        $theme = DauxHelper::get_theme($theme_file, $local_base);

        if (!isset($theme['favicon'])){
            $theme['favicon'] = utf8_encode($base_url . 'img/favicon.png');
        } else {
            $theme['favicon'] = utf8_encode(str_replace('<base_url>', $base_url, $theme['favicon']));
            $theme['favicon'] = str_replace('<theme_url>', $theme_url, $theme['favicon']);
        }

        foreach ($theme['css'] as $key => $css) {
            $theme['css'][$key] = utf8_encode(str_replace('<base_url>', $base_url, $css));
            $theme['css'][$key] = utf8_encode(str_replace('<theme_url>', $theme_url, $css));
        }

        foreach ($theme['fonts'] as $key => $font) {
            $theme['fonts'][$key] = utf8_encode(str_replace('<base_url>', $base_url, $font));
            $theme['fonts'][$key] = utf8_encode(str_replace('<theme_url>', $theme_url, $font));
        }

        foreach ($theme['js'] as $key => $js) {
            $theme['js'][$key] = utf8_encode(str_replace('<base_url>', $base_url, $js));
            $theme['js'][$key] = utf8_encode(str_replace('<theme_url>', $theme_url, $js));
        }

        return $theme;
    }

    public static function get_error_params(Daux $daux)
    {
        $params = $daux->get_base_params();
        $params['theme'] = Helper::configure_theme(
            $daux->local_base . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $daux->options['template'] . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . $daux->options['theme'],
            $params['base_url'],
            $daux->local_base,
            $params['base_url'] . "templates/" . $params['template'] . "/themes/" . $daux->options['theme'] . '/'
        );

        $params['index_key'] = 'index';

        $protocol = '//';
        $params['base_url'] = $protocol . $daux->base_url;
        $params['base_page'] = $params['base_url'];
        $params['host'] = $daux->host;

        $params['clean_urls'] = $daux->options['clean_urls'];

        if ($params['image'] !== '') $params['image'] = str_replace('<base_url>', $params['base_url'], $params['image']);

        return $params;
    }

    public static function get_request()
    {
        if (isset($_SERVER['PATH_INFO'])) $uri = $_SERVER['PATH_INFO'];
        else if (isset($_SERVER['REQUEST_URI'])) {
            $uri = $_SERVER['REQUEST_URI'];
            if (strpos($uri, $_SERVER['SCRIPT_NAME']) === 0) $uri = substr($uri, strlen($_SERVER['SCRIPT_NAME']));
            else if (strpos($uri, dirname($_SERVER['SCRIPT_NAME'])) === 0) $uri = substr($uri, strlen(dirname($_SERVER['SCRIPT_NAME'])));
            if (strncmp($uri, '?/', 2) === 0) $uri = substr($uri, 2);
            $parts = preg_split('#\?#i', $uri, 2);
            $uri = $parts[0];
            if (isset($parts[1])) {
                $_SERVER['QUERY_STRING'] = $parts[1];
                parse_str($_SERVER['QUERY_STRING'], $_GET);
            } else {
                $_SERVER['QUERY_STRING'] = '';
                $_GET = array();
            }
            $uri = parse_url($uri, PHP_URL_PATH);
        }
        else return false;
        $uri = str_replace(array('//', '../'), '/', trim($uri, '/'));
        if ($uri == "") $uri = "first_page";
        return $uri;
    }
}
