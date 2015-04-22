<?php namespace Todaymade\Daux\Generator;

use Todaymade\Daux\Daux;
use Todaymade\Daux\MarkdownPage;
use Todaymade\Daux\Tree\Directory;
use Todaymade\Daux\Tree\Content;

class Generator
{
    public function generate($global_config, $destination)
    {
        $daux = new Daux(Daux::STATIC_MODE);
        $daux->initialize($global_config);

        $params = $daux->getParams();
        if (is_null($destination)) {
            $destination = $daux->local_base . DS . 'static';
        }

        Helper::copyAssets($destination, $daux->local_base);

        $this->generateRecursive($daux->tree, $destination, $params);
    }

    private function generateRecursive($tree, $output_dir, $params, $base_url = '')
    {
        $params['base_url'] = $params['base_page'] = $base_url;
        $new_params = $params;
        //
        $params['image'] = str_replace('<base_url>', $base_url, $params['image']);
        if ($base_url !== '') {
            $params['entry_page'] = $tree->getFirstPage();
        }
        foreach ($tree->value as $key => $node) {
            if ($node instanceof Directory) {
                $new_output_dir = $output_dir . DS . $key;
                @mkdir($new_output_dir);
                $this->generateRecursive($node, $new_output_dir, $new_params, '../' . $base_url);
            } elseif ($node instanceof Content) {
                $params['request'] = $node->getUrl();
                $params['file_uri'] = $node->getName();

                $page = MarkdownPage::fromFile($node, $params);
                file_put_contents($output_dir . DS . $key, $page->getContent());
            } else {
                copy($node->getPath(), $output_dir . DS . $key);
            }
        }
    }
}
