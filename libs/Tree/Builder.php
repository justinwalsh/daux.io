<?php namespace Todaymade\Daux\Tree;

use Todaymade\Daux\Daux;
use Todaymade\Daux\DauxHelper;

class Builder
{
    public static function build($dir, $ignore, $params, $parents = null)
    {
        if (!$dh = opendir($dir)) {
            return;
        }

        $node = new Directory($dir, $parents);

        $new_parents = $parents;
        if (is_null($new_parents)) {
            $new_parents = array();
        } else {
            $new_parents[] = $node;
        }

        while (($file = readdir($dh)) !== false) {
            if ($file == '.' || $file == '..') {
                continue;
            }

            $path = $dir . DS . $file;

            if (is_dir($path) && in_array($file, $ignore['folders'])) {
                continue;
            }
            if (!is_dir($path) && in_array($file, $ignore['files'])) {
                continue;
            }

            $entry = null;
            if (is_dir($path)) {
                $entry = static::build($path, $ignore, $params, $new_parents);
            } elseif (in_array(pathinfo($path, PATHINFO_EXTENSION), Daux::$VALID_MARKDOWN_EXTENSIONS)) {
                $entry = new Content($path, $new_parents);

                if ($params['mode'] === Daux::STATIC_MODE) {
                    $entry->setUri($entry->getUri() . '.html');
                }
            } else {
                $entry = new Raw($path, $new_parents);
            }

            if ($entry instanceof Entry) {
                $node->value[$entry->getUri()] = $entry;
            }
        }

        $node->sort();
        if (isset($node->value[$params['index_key']])) {
            $node->value[$params['index_key']]->setFirstPage($node->getFirstPage());
            $node->setIndexPage($node->value[$params['index_key']]);
        } else {
            $node->setIndexPage(false);
        }
        return $node;
    }

    public static function getOrCreateDir($parent, $title) {
        $slug = DauxHelper::slug($title);

        if (array_key_exists($slug, $parent->value)) {
            return $parent->value[$slug];
        }

        $dir = new Directory();
        $dir->setTitle($title);
        $dir->setUri($slug);
        $parent->value[$slug] = $dir;

        return $dir;
    }

    public static function getOrCreatePage($parents, $title) {
        $slug = DauxHelper::slug($title);
        $uri = $slug . ".html";

        $nearestParent = end($parents);

        if (array_key_exists($uri, $nearestParent->value)) {
            return $nearestParent->value[$uri];
        }

        $page = new Content('', $parents);
        $page->setUri($uri);
        $page->setContent("-"); //set an almost empty content to avoid problems

        if ($title == 'index') {
            $page->setName('_index');
            $page->setTitle($nearestParent->getTitle());
            $page->value = 'index';
            $nearestParent->setIndexPage($page);
        } else {
            $page->setName($slug);
            $page->setTitle($title);
        }

        $nearestParent->value[$uri] = $page;

        return $page;
    }
}
