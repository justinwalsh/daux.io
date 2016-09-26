<?php namespace Todaymade\Daux\Tree;

use RuntimeException;
use SplFileInfo;
use Todaymade\Daux\DauxHelper;

class Builder
{
    protected static $IGNORED = [
        // Popular VCS Systems
        '.svn', '_svn', 'CVS', '_darcs', '.arch-params', '.monotone', '.bzr', '.git', '.hg',

        // Operating system files
        '.DS_Store', 'Thumbs.db',
    ];

    protected static function isIgnored(\SplFileInfo $file, $ignore)
    {
        $filename = $file->getFilename();

        if (in_array($filename, static::$IGNORED)) {
            return true;
        }

        if (array_key_exists('folders', $ignore) && $file->isDir() && in_array($filename, $ignore['folders'])) {
            return true;
        }

        if (array_key_exists('files', $ignore) && !$file->isDir() && in_array($filename, $ignore['files'])) {
            return true;
        }

        return false;
    }

    /**
     * Get name for a file
     *
     * @param string $path
     * @return string
     */
    protected static function getName($path)
    {
        // ['dir' => 1, 'basename' => 2, 'filename' => 3, 'extension' => 5]
        preg_match('%^(.*?)[\\\\/]*(([^/\\\\]*?)(\.([^\.\\\\/]+?)|))[\\\\/\.]*$%im', $path, $m);

        if (!isset($m[3])) {
            throw new RuntimeException('Name not found');
        }

        return $m[3];
    }

    /**
     * Build the initial tree
     *
     * @param Directory $node
     * @param array $ignore
     */
    public static function build($node, $ignore)
    {
        if (($it = new \FilesystemIterator($node->getPath())) == false) {
            return;
        }

        if ($node instanceof Root) {
            // Ignore config.json in the root directory
            $ignore['files'][] = 'config.json';
        }

        /** @var \SplFileInfo $file */
        foreach ($it as $file) {
            if (static::isIgnored($file, $ignore)) {
                continue;
            }

            if ($file->isDir()) {
                $new = new Directory($node, static::removeSortingInformations($file->getFilename()), $file);
                $new->setName(static::getName($file->getPathName()));
                $new->setTitle(str_replace('_', ' ', static::removeSortingInformations($new->getName())));
                static::build($new, $ignore);
            } else {
                static::createContent($node, $file);
            }
        }

        $node->sort();
    }

    /**
     * @param Directory $parent
     * @param SplFileInfo $file
     * @return Content|Raw
     */
    public static function createContent(Directory $parent, SplFileInfo $file)
    {
        $name = static::getName($file->getPathname());

        $config = $parent->getConfig();

        if (!in_array($file->getExtension(), $config['valid_content_extensions'])) {
            $uri = static::removeSortingInformations($file->getFilename());

            $entry = new Raw($parent, $uri, $file);
            $entry->setTitle(str_replace('_', ' ', static::removeSortingInformations($name)));
            $entry->setName($name);

            return $entry;
        }

        $uri = static::removeSortingInformations($name);
        if ($config->isStatic()) {
            $uri .= '.html';
        }

        $entry = new Content($parent, $uri, $file);

        if ($entry->getUri() == $config['index_key']) {
            if ($parent instanceof Root) {
                $entry->setTitle($config['title']);
            } else {
                $entry->setTitle($parent->getTitle());
            }
        } else {
            $entry->setTitle(str_replace('_', ' ', static::removeSortingInformations($name)));
        }

        $entry->setName($name);

        return $entry;
    }

    /**
     * @param string $filename
     * @return string
     */
    public static function removeSortingInformations($filename)
    {
        preg_match('/^-?[0-9]*_?(.*)/', $filename, $matches);

        // Remove the numeric part
        // of the filename, only if
        // there is something after
        return empty($matches[1]) ? $matches[0] : $matches[1];
    }

    /**
     * @param Directory $parent
     * @param string $title
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
     * @return ContentAbstract
     */
    public static function getOrCreatePage(Directory $parent, $path)
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        // If the file doesn't have an extension, set .md as a default
        if ($extension == '') {
            $extension = 'md';
            $path .= '.md';
        }

        $raw = !in_array($extension, $parent->getConfig()['valid_content_extensions']);

        $title = $uri = $path;
        if (!$raw) {
            $title = static::getName($path);
            $uri = DauxHelper::slug($title);
            if ($parent->getConfig()->isStatic()) {
                $uri .= '.html';
            }
        }

        if (array_key_exists($uri, $parent->getEntries())) {
            return $parent->getEntries()[$uri];
        }

        $page = $raw ? new ComputedRaw($parent, $uri) : new Content($parent, $uri);
        $page->setContent('-'); //set an almost empty content to avoid problems
        $page->setName($path);
        $page->setTitle($title);

        if ($title == 'index' || $title == '_index') {
            $page->setTitle($parent->getTitle());
        }

        return $page;
    }
}
