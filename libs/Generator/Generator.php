<?php namespace Todaymade\Daux\Generator;

use Todaymade\Daux\Daux;
use Todaymade\Daux\Entry;
use Todaymade\Daux\MarkdownPage;

class Generator {
    public function generate($global_config, $destination) {
        $daux = new Daux(Daux::STATIC_MODE);
        $daux->initialize($global_config);

        $this->generate_static($daux, $destination);
    }

    public function generate_static(Daux $daux, $output_dir = NULL) {
        $params = $daux->get_page_params();
        if (is_null($output_dir)) $output_dir = $daux->local_base . DIRECTORY_SEPARATOR . 'static';
        Helper::clean_copy_assets($output_dir, $daux->local_base);
        $this->recursive_generate_static($daux->tree, $output_dir, $params);
    }

    private function recursive_generate_static($tree, $output_dir, $params, $base_url = '') {
        $params['base_url'] = $params['base_page'] = $base_url;
        $new_params = $params;
        //changed this as well in order for the templates to be put in the right place
        $params['theme'] = Helper::rebase_theme($params['theme'], $base_url, $params['base_url'] . "templates/default/themes/" . $params['theme']['name'] . '/');
        //
        $params['image'] = str_replace('<base_url>', $base_url, $params['image']);
        if ($base_url !== '') $params['entry_page'] = $tree->first_page;
        foreach ($tree->value as $key => $node) {
            if ($node->type === Entry::DIRECTORY_TYPE) {
                $new_output_dir = $output_dir . DIRECTORY_SEPARATOR . $key;
                @mkdir($new_output_dir);
                $this->recursive_generate_static($node, $new_output_dir, $new_params, '../' . $base_url);
            } else {
                $params['request'] = $node->get_url();
                $params['file_uri'] = $node->name;

                $page = MarkdownPage::fromFile($node, $params);
                file_put_contents($output_dir . DIRECTORY_SEPARATOR . $key, $page->get_page_content());
            }
        }
    }


}
