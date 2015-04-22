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

            $file_details = DauxHelper::pathinfo($path);

            $entry = null;
            if (is_dir($path)) {
                $entry = static::build($path, $ignore, $params, $new_parents);
            } elseif (in_array($file_details['extension'], Daux::$VALID_MARKDOWN_EXTENSIONS)) {
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
}
