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
        // Use internal templates or the ones in the phar
        // archive if no templates dir exists in the working directory
        if (!is_dir($base)) {
            if (is_dir(__DIR__ . '/../../../templates')) {
                $base = __DIR__ . '/../../../templates';
            } else {
                $base = 'phar://daux.phar/templates';
            }
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
    public function render($name, array $data = [])
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
        $this->engine->registerFunction('get_navigation', function ($tree, $path, $current_url, $base_page, $mode) {
            $nav = $this->buildNavigation($tree, $path, $current_url, $base_page, $mode);

            return $this->renderNavigation($nav);
        });

        $this->engine->registerFunction('get_breadcrumb_title', function ($page, $base_page) {
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
        $nav = '';
        foreach ($entries as $entry) {
            if (array_key_exists('children', $entry)) {
                $icon = '<i class="Nav__arrow">&nbsp;</i>';

                if (array_key_exists('href', $entry)) {
                    $link = '<a href="' . $entry['href'] . '" class="folder">' . $icon . $entry['title'] . '</a>';
                } else {
                    $link = '<a href="#" class="aj-nav folder">' . $icon . $entry['title'] . '</a>';
                }

                $link .= $this->renderNavigation($entry['children']);
            } else {
                $link = '<a href="' . $entry['href'] . '">' . $entry['title'] . '</a>';
            }

            $nav .= "<li class='Nav__item $entry[class]'>$link</li>";
        }

        return "<ul class='Nav'>$nav</ul>";
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
                    'class' => $node->isHotPath() ? 'Nav__item--active' : '',
                ];
            } elseif ($node instanceof Directory) {
                if (!$node->hasContent()) {
                    continue;
                }

                $folder = [
                    'title' => $node->getTitle(),
                    'class' => $node->isHotPath() ? 'Nav__item--open' : '',
                ];

                if ($index = $node->getIndexPage()) {
                    $folder['href'] = $base_page . $index->getUrl();
                }

                //Child pages
                $new_path = ($path === '') ? $url : $path . '/' . $url;
                $folder['children'] = $this->buildNavigation($node, $new_path, $current_url, $base_page, $mode);

                if (!empty($folder['children'])) {
                    $folder['class'] .= ' has-children';
                }

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
                return ' <svg class="Page__header--separator" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 477.175 477.175"><path d="M360.73 229.075l-225.1-225.1c-5.3-5.3-13.8-5.3-19.1 0s-5.3 13.8 0 19.1l215.5 215.5-215.5 215.5c-5.3 5.3-5.3 13.8 0 19.1 2.6 2.6 6.1 4 9.5 4 3.4 0 6.9-1.3 9.5-4l225.1-225.1c5.3-5.2 5.3-13.8.1-19z"/></svg> ';
            default:
                return $separator;
        }
    }
}
