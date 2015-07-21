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

                /**
                 * Check folder for configuration overrides
                 */
                $params_custom = $params;
                if( is_file($path . DIRECTORY_SEPARATOR . 'config.json') ){
                    $config = json_decode(file_get_contents($path . DIRECTORY_SEPARATOR . 'config.json'), true);
                    if( !empty($config) ){
                        $params_custom = array_merge($params_custom, $config);
                    }
                }

                /**
                 * Abort descendent lookup if failed ipfilter
                 */
                if( !empty($params_custom['ipfilter']) ){
                    if( !in_array($_SERVER['REMOTE_ADDR'], $params_custom['ipfilter']) ){
                        continue;
                    }
                }

                /**
                 * Traverse path for descendents
                 * @var [type]
                 */
                $entry = static::build($path, $ignore, $params_custom, $new_parents);

            }
            else if (in_array(pathinfo($path, PATHINFO_EXTENSION), Daux::$VALID_MARKDOWN_EXTENSIONS)) {
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

        /**
         * Assign the first child content available as node index
         */
        if( $params['inherit_index'] ){
            $node->setInheritedIndex();
        }

        /**
         * Assign a descendent matching the 'index_key' exists as node index
         */
        else if (isset($node->value[$params['index_key']])) {
            $node->value[$params['index_key']]->setFirstPage($node->getFirstPage());
            $node->setIndexPage($node->value[$params['index_key']]);
        }

        /**
         * Indicate a missing node index
         */
        else {
            $node->setIndexPage(false);
        }

        return $node;
    }
}
