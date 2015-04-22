<?php namespace Todaymade\Daux\Tree;

use Todaymade\Daux\Daux;
use Todaymade\Daux\DauxHelper;

class Builder {
    public static function build($dir, $ignore, $mode = Daux::LIVE_MODE, $parents = null) {
        if (!$dh = opendir($dir)) {
            return;
        }

        $node = new Directory($dir, $parents);

        $new_parents = $parents;
        if (is_null($new_parents)) {
            $new_parents = array();
        } else{
            $new_parents[] = $node;
        }

        while (($file = readdir($dh)) !== false) {
            if ($file == '.' || $file == '..') {
                continue;
            }

            $path = $dir . DIRECTORY_SEPARATOR . $file;

            if ((is_dir($path) && in_array($file, $ignore['folders'])) || (!is_dir($path) && in_array($file, $ignore['files']))) {
                continue;
            }

            $file_details = DauxHelper::pathinfo($path);

            $entry = null;
            if (is_dir($path)) {
                $entry = static::build($path, $ignore, $mode, $new_parents);
            } else if(in_array($file_details['extension'], Daux::$VALID_MARKDOWN_EXTENSIONS)) {
                $entry = new Content($path, $new_parents);

                if ($mode === Daux::STATIC_MODE) {
                    $entry->uri .= '.html';
                }
            } else {
                $entry = new Raw($path, $new_parents);
            }

            if ($entry instanceof Entry) {
                $node->value[$entry->uri] = $entry;
            }
        }

        $node->sort();
        $node->first_page = $node->get_first_page();
        $index_key = ($mode === Daux::LIVE_MODE) ? 'index' : 'index.html';
        if (isset($node->value[$index_key])) {
            $node->value[$index_key]->first_page = $node->first_page;
            $node->index_page =  $node->value[$index_key];
        } else $node->index_page = false;
        return $node;

    }
}
