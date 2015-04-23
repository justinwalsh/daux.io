<?php namespace Todaymade\Daux;

use League\Plates\Engine;

class Template
{

    protected $engine;

    public function __construct($base, $theme)
    {
        // Create new Plates instance
        $this->engine = new Engine($base);
        if (!is_dir($theme)) {
            $theme = $base;
        }
        $this->engine->addFolder('theme', $theme, true);

        $this->registerFunctions();
    }

    public function render($name, array $data = array())
    {

        $this->engine->addData([
            'index' => utf8_encode($data['params']['base_page'] . $data['params']['index']->value),
            'base_url' => $data['params']['base_url'],
            'base_page' => $data['params']['base_page'],
            'page' => $data['page'],
            'params' => $data['params'],
            //'homepage' => $data['params']['homepage'],
            //'project_title' => utf8_encode($data['params']['title']),
            'tree' => $data['params']['tree'],
            //'entry_page' => $data['page']['entry_page'],
        ]);

        return $this->engine->render($name, $data);
    }

    protected function registerFunctions()
    {
        $this->engine->registerFunction('get_navigation', function($tree, $path, $current_url, $base_page, $mode) {
            $nav = '<ul class="nav nav-list">';
            $nav .= $this->buildNavigation($tree, $path, $current_url, $base_page, $mode);
            $nav .= '</ul>';
            return $nav;
        });

        $this->engine->registerFunction('get_breadcrumb_title', function($page, $base_page) {
            $title = '';
            $breadcrumb_trail = $page['breadcrumb_trail'];
            $separator = $this->getSeparator($page['breadcrumb_separator']);
            foreach ($breadcrumb_trail as $key => $value) {
                $title .= '<a href="' . $base_page . $value . '">' . $key . '</a>' . $separator;
            }
            if ($page['filename'] === 'index' || $page['filename'] === '_index') {
                if ($page['title'] != '') {
                    $title = substr($title, 0, -1 * strlen($separator));
                }
            } else {
                $title .= '<a href="' . $base_page . $page['request'] . '">' . $page['title'] . '</a>';
            }
            return $title;
        });
    }

    private function buildNavigation($tree, $path, $current_url, $base_page, $mode)
    {
        $nav = '';
        foreach ($tree->value as $node) {
            $url = $node->getUri();
            if ($node instanceof \Todaymade\Daux\Tree\Content) {
                if ($node->value === 'index') {
                    continue;
                }
                $nav .= '<li';
                $link = ($path === '') ? $url : $path . '/' . $url;
                if ($current_url === $link) {
                    $nav .= ' class="active"';
                }
                $nav .= '><a href="' . $base_page . $link . '">' . $node->getTitle() . '</a></li>';
            }
            if ($node instanceof \Todaymade\Daux\Tree\Directory) {
                $nav .= '<li';
                $link = ($path === '') ? $url : $path . '/' . $url;
                if (strpos($current_url, $link) === 0) {
                    $nav .= ' class="open"';
                }
                $nav .= ">";
                if ($mode === \TodayMade\Daux\Daux::STATIC_MODE) {
                    $link .= "/index.html";
                }
                if ($node->getIndexPage()) {
                    $nav .= '<a href="' . $base_page . $link . '" class="folder">' .
                    $node->getTitle() . '</a>';
                } else {
                    $nav .= '<a href="#" class="aj-nav folder">' . $node->getTitle() . '</a>';
                }
                $nav .= '<ul class="nav nav-list">';
                $new_path = ($path === '') ? $url : $path . '/' . $url;
                $nav .= $this->buildNavigation($node, $new_path, $current_url, $base_page, $mode);
                $nav .= '</ul></li>';
            }
        }
        return $nav;
    }

    private function getSeparator($separator)
    {
        switch ($separator) {
            case 'Chevrons':
                return ' <i class="glyphicon glyphicon-chevron-right"></i> ';
            default:
                return $separator;
        }
    }
}
