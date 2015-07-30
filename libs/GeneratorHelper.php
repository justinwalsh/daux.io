<?php namespace Todaymade\Daux;

class GeneratorHelper
{
    /**
     * Copy all files from $path to $local_base
     *
     * @param string $path
     * @param string $local_base
     */
    public static function copyAssets($path, $local_base)
    {
        if (is_dir($path)) {
            static::rmdir($path);
        } else {
            mkdir($path);
        }

        mkdir($path . DIRECTORY_SEPARATOR . 'resources');
        static::copyRecursive(
            $local_base . DIRECTORY_SEPARATOR . 'resources',
            $path . DIRECTORY_SEPARATOR . 'resources'
        );
    }

    /**
     * Remove a directory recursively
     *
     * @param string $dir
     */
    protected static function rmdir($dir)
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

    /**
     * Copy files recursively
     *
     * @param string $source
     * @param string $destination
     */
    protected static function copyRecursive($source, $destination)
    {
        if (!is_dir($destination)) {
            mkdir($destination);
        }

        $dir = opendir($source);
        while (false !== ($file = readdir($dir))) {
            if ($file != '.' && $file != '..') {
                if (is_dir($source . '/' . $file)) {
                    static::copyRecursive($source . '/' . $file, $destination . '/' . $file);
                } else {
                    copy($source . '/' . $file, $destination . '/' . $file);
                }
            }
        }
        closedir($dir);
    }
}
