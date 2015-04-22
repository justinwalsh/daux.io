<?php namespace Todaymade\Daux\Server;

use Todaymade\Daux\Daux;
use Todaymade\Daux\DauxHelper;

class Helper {
    public static function get_error_params(Daux $daux)
    {
        $params = $daux->get_base_params();
        $params['theme'] = DauxHelper::get_theme(
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
