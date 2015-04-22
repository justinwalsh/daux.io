<?php namespace Todaymade\Daux\Generator;

class Helper
{

    public static function copyAssets($path, $local_base)
    {
        @mkdir($path);
        static::rmdir($path);

        @mkdir($path . DS . 'resources');
        static::copyRecursive($local_base . DS . 'resources', $path . DS . 'resources');
        @mkdir($path . DS . 'js');
        static::copyRecursive($local_base . DS . 'js', $path . DS . 'js');

        //added and changed these in order to fetch the theme files and put them in the right place
        @mkdir($path . DS . 'templates');
        @mkdir($path . DS . 'templates' . DS . 'default');
        @mkdir($path . DS . 'templates' . DS . 'default' . DS . 'themes');

        static::copyRecursive(
            $local_base . DS . 'templates' . DS . 'default' . DS . 'themes',
            $path . DS . 'templates' . DS . 'default' . DS . 'themes'
        );
    }

    private static function rmdir($dir)
    {
        $it = new \RecursiveDirectoryIterator($dir);
        $files = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($files as $file) {
            if ($file->getFilename() === '.' || $file->getFilename() === '..') {
                continue;
            }
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
    }

    private static function copyRecursive($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ( $file = readdir($dir))) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if (is_dir($src . '/' . $file)) {
                    static::copyRecursive($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }
}
