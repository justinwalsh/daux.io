<?php namespace Todaymade\Daux\Generator;

use Todaymade\Daux\DauxHelper;

class Helper {

    public static function clean_copy_assets($path, $local_base){
        @mkdir($path);
        static::clean_directory($path);

        @mkdir($path . DIRECTORY_SEPARATOR . 'img');
        static::copy_recursive($local_base . DIRECTORY_SEPARATOR . 'img', $path . DIRECTORY_SEPARATOR . 'img');
        @mkdir($path . DIRECTORY_SEPARATOR . 'js');
        static::copy_recursive($local_base . DIRECTORY_SEPARATOR . 'js', $path . DIRECTORY_SEPARATOR . 'js');
        //added and changed these in order to fetch the theme files and put them in the right place
        @mkdir($path . DIRECTORY_SEPARATOR . 'templates');
        @mkdir($path . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'default');
        @mkdir($path . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . 'themes');
        static::copy_recursive(
            $local_base . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . 'themes',
            $path . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . 'themes'
        );
    }

    //  Rmdir
    private static function clean_directory($dir) {
        $it = new \RecursiveDirectoryIterator($dir);
        $files = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);
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

    public static function rebase_theme($theme, $base_url, $theme_url) {
        $theme['favicon'] = utf8_encode(str_replace('<base_url>', $base_url, $theme['favicon']));
        $theme['favicon'] = str_replace('<theme_url>', $theme_url, $theme['favicon']);

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

    public static function configure_theme($theme, $base_url, $local_base, $theme_url) {
        $theme = DauxHelper::get_theme($theme, $local_base);

        if (!isset($theme['favicon'])) $theme['favicon'] = '<base_url>img/favicon.png';

        return $theme;
    }
}
