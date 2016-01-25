<?php namespace Todaymade\Daux\Format\HTML;

use League\Plates\Engine;
use Todaymade\Daux\Daux;
use Todaymade\Daux\Tree\Content;
use Todaymade\Daux\Tree\Directory;

class Template
{
    protected $engine;

    /**
     * @param string $base
     * @param string $theme
     */
    public function __construct($base, $theme)
    {
        // Use templates from the phar archive if the templates dir doesn't exist.
        if (!is_dir($base)) {
            $base = 'phar://daux.phar/templates';
        }

        // Create new Plates instance
        $this->engine = new Engine($base);
        if (!is_dir($theme)) {
            $theme = $base;
        }
        $this->engine->addFolder('theme', $theme, true);

        $this->registerFunctions();
    }

    /**
     * @param string $name
     * @param array $data
     * @return string
     */
    public function render($name, array $data = array())
    {
        $this->engine->addData([
            'base_url' => $data['params']['base_url'],
            'base_page' => $data['params']['base_page'],
            'page' => $data['page'],
            'params' => $data['params'],
            'tree' => $data['params']['tree'],
        ]);

        return $this->engine->render($name, $data);
    }

    protected function registerFunctions()
    {
        $this->engine->registerFunction('get_navigation', function($tree, $path, $current_url, $base_page, $mode) {
            $nav = $this->buildNavigation($tree, $path, $current_url, $base_page, $mode);
            return $this->renderNavigation($nav);
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

    private function renderNavigation($entries)
    {
        $nav = "";
        foreach ($entries as $entry) {
            if (array_key_exists('children', $entry)) {
                if (array_key_exists('href', $entry)) {
                    $link = '<a href="' . $entry['href'] . '" class="folder">' . $entry['title'] . '</a>';
                } else {
                    $link = '<a href="#" class="aj-nav folder">' . $entry['title'] . '</a>';
                }

                $link .= $this->renderNavigation($entry['children']);
            } else {
                $link = '<a href="' . $entry['href'] . '">' . $entry['title'] . '</a>';
            }

            $nav .= "<li class='$entry[class]'>$link</li>";
        }

        return "<ul class='nav nav-list'>$nav</ul>";
    }

    private function buildNavigation(Directory $tree, $path, $current_url, $base_page, $mode)
    {
        $nav = [];
        foreach ($tree->getEntries() as $node) {
            $url = $node->getUri();
            if ($node instanceof Content) {
                if ($node->isIndex()) {
                    continue;
                }

                $link = ($path === '') ? $url : $path . '/' . $url;

                $nav[] = [
                    'title' => $node->getTitle(),
                    'href' => $base_page . $link,
                    'class' => ($current_url === $link) ? 'active' : ''
                ];
            } elseif ($node instanceof Directory) {
                if (!$node->hasContent()) {
                    continue;
                }

                $link = ($path === '') ? $url : $path . '/' . $url;

                $folder = [
                    'title' => $node->getTitle(),
                    'class' => (strpos($current_url, $link) === 0) ? 'open' : '',
                ];

                if ($mode === Daux::STATIC_MODE) {
                    $link .= "/index.html";
                }

                if ($node->getIndexPage()) {
                    $folder['href'] = $base_page . $link;
                }

                //Child pages
                $new_path = ($path === '') ? $url : $path . '/' . $url;
                $folder['children'] = $this->buildNavigation($node, $new_path, $current_url, $base_page, $mode);

                $nav[] = $folder;
            }
        }
        return $nav;
    }

    /**
     * @param string $separator
     * @return string
     */
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
