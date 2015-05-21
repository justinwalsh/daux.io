<?php namespace Todaymade\Daux\Format\HTML;

use Todaymade\Daux\Daux;
use Todaymade\Daux\DauxHelper;
use Todaymade\Daux\Generator\Helper;
use Todaymade\Daux\Tree\Directory;
use Todaymade\Daux\Tree\Content;

class Generator
{
    public function generate(Daux $daux, $destination)
    {
        $params = $daux->getParams();
        if (is_null($destination)) {
            $destination = $daux->local_base . DS . 'static';
        }

        echo "Copying Static assets ...\n";
        Helper::copyAssets($destination, $daux->local_base);

        echo "Generating ...\n";
        $this->generateRecursive($daux->tree, $destination, $params);
        echo "Done !\n";
    }

    private function generateRecursive($tree, $output_dir, $params, $base_url = '')
    {
        $params['base_url'] = $params['base_page'] = $base_url;

        // Rebase Theme
        $params['theme'] = DauxHelper::getTheme(
            $params['theme-name'],
            $params['base_url'],
            $params['local_base'],
            $base_url
        );

        $params['image'] = str_replace('<base_url>', $base_url, $params['image']);
        if ($base_url !== '') {
            $params['entry_page'] = $tree->getFirstPage();
        }
        foreach ($tree->value as $key => $node) {
            if ($node instanceof Directory) {
                $new_output_dir = $output_dir . DS . $key;
                @mkdir($new_output_dir);
                $this->generateRecursive($node, $new_output_dir, $params, '../' . $base_url);
            } elseif ($node instanceof Content) {
                echo "- " . $node->getUrl() . "\n";
                $params['request'] = $node->getUrl();
                $params['file_uri'] = $node->getName();

                $page = MarkdownPage::fromFile($node, $params);
                file_put_contents($output_dir . DS . $key, $page->getContent());
            } else {
                echo "- " . $node->getUrl() . "\n";
                copy($node->getPath(), $output_dir . DS . $key);
            }
        }
    }
}
