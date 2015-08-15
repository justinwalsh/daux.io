<?php namespace Todaymade\Daux\Tree;

use Todaymade\Daux\Daux;
use Todaymade\Daux\DauxHelper;

class Builder
{
    /**
     * Build the initial tree
     *
     * @param Directory $node
     * @param array $ignore
     */
    public static function build($node, $ignore)
    {
        if (!$dh = opendir($node->getPath())) {
            return;
        }

        if ($node instanceof Root) {
            // Ignore config.json in the root directory
            $ignore['files'][] = 'config.json';
        }

        while (($file = readdir($dh)) !== false) {
            if ($file == '.' || $file == '..') {
                continue;
            }

            $path = $node->getPath() . DIRECTORY_SEPARATOR . $file;

            if (is_dir($path) && in_array($file, $ignore['folders'])) {
                continue;
            }
            if (!is_dir($path) && in_array($file, $ignore['files'])) {
                continue;
            }

            if (is_dir($path)) {
                $new = new Directory($node, static::removeSortingInformations(static::getFilename($path)), $path);
                $new->setName(DauxHelper::pathinfo($path)['filename']);
                $new->setTitle(static::removeSortingInformations($new->getName(), ' '));
                static::build($new, $ignore);
            } else {
                static::createContent($node, $path);
            }
        }

        $node->sort();
    }

    /**
     * @param Directory $parent
     * @param string $path
     * @return Content|Raw
     */
    public static function createContent(Directory $parent, $path)
    {
        $name = DauxHelper::pathinfo($path)['filename'];

        $config = $parent->getConfig();

        if (!in_array(pathinfo($path, PATHINFO_EXTENSION), $config['valid_content_extensions'])) {
            $uri = static::removeSortingInformations(static::getFilename($path));

            $entry = new Raw($parent, $uri, $path, filemtime($path));
            $entry->setTitle(static::removeSortingInformations($name, ' '));
            $entry->setName($name);

            return $entry;
        }

        $uri = static::removeSortingInformations($name);
        if ($config['mode'] === Daux::STATIC_MODE) {
            $uri .= '.html';
        }

        $entry = new Content($parent, $uri, $path, filemtime($path));

        if ($entry->getUri() == $config['index_key']) {
            if ($parent instanceof Root) {
                $entry->setTitle($config['title']);
            } else {
                $entry->setTitle($parent->getTitle());
            }
        } else {
            $entry->setTitle(static::removeSortingInformations($name, ' '));
        }

        $entry->setName($name);

        return $entry;
    }

    /**
     * @param string $file
     * @return string
     */
    protected static function getFilename($file)
    {
        $parts = explode(DIRECTORY_SEPARATOR, $file);
        return end($parts);
    }

    /**
     * @param string $filename
     * @return string
     */
    protected static function removeSortingInformations($filename, $separator = '_')
    {
        $filename = explode('_', $filename);

        // Remove the numeric part of the
        // filename, only if there is
        // something after that
        if ($filename[0] == '' || (is_numeric($filename[0]) && array_key_exists(1, $filename))) {
            unset($filename[0]);
        } else {
            $t = $filename[0];
            if ($t[0] == '-') {
                $filename[0] = substr($t, 1);
            }
        }
        $filename = implode($separator, $filename);
        return $filename;
    }

    /**
     * @param Directory $parent
     * @param String $title
     * @return Directory
     */
    public static function getOrCreateDir(Directory $parent, $title)
    {
        $slug = DauxHelper::slug($title);

        if (array_key_exists($slug, $parent->getEntries())) {
            return $parent->getEntries()[$slug];
        }

        $dir = new Directory($parent, $slug);
        $dir->setTitle($title);

        return $dir;
    }

    /**
     * @param Directory $parent
     * @param string $path
     * @return Content
     */
    public static function getOrCreatePage(Directory $parent, $path)
    {
        $title = DauxHelper::pathinfo($path)['filename'];

        // If the file doesn't have an extension, set .md as a default
        if (DauxHelper::pathinfo($path)['extension'] == '') {
            $path .= '.md';
        }

        $uri = $slug = DauxHelper::slug($title);
        if ($parent->getConfig()['mode'] === Daux::STATIC_MODE) {
            $uri = $slug . ".html";
        }

        if (array_key_exists($uri, $parent->getEntries())) {
            return $parent->getEntries()[$uri];
        }

        $page = new Content($parent, $uri);
        $page->setContent("-"); //set an almost empty content to avoid problems

        if ($title == 'index') {
            // TODO :: clarify the difference between 'index' and '_index'
            $page->setName('_index' . DauxHelper::pathinfo($path)['extension']);
            $page->setTitle($parent->getTitle());
        } else {
            $page->setName($path);
            $page->setTitle($title);
        }

        return $page;
    }
}
