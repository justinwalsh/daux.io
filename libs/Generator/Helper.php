<?php namespace Todaymade\Daux\Generator;

class Helper
{
    /**
     * Copy all files from $path to $local_base
     *
     * @param string $path
     * @param string $local_base
     */
    public static function copyAssets($path, $local_base)
    {
        mkdir($path);
        static::rmdir($path);

        mkdir($path . DS . 'resources');
        static::copyRecursive($local_base . DS . 'resources', $path . DS . 'resources');
    }

    /**
     * Remove a directory recursively
     *
     * @param string $dir
     */
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

    /**
     * Copy files recursively
     *
     * @param string $source
     * @param string $destination
     */
    private static function copyRecursive($source, $destination)
    {
        $dir = opendir($source);
        mkdir($destination);
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
