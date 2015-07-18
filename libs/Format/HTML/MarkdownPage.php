<?php namespace Todaymade\Daux\Format\HTML;

class MarkdownPage extends \Todaymade\Daux\Format\Base\MarkdownPage
{
    private $language;
    private $homepage;

    private function initialize()
    {
        $this->homepage = false;
        if ($this->title === 'index') {
            $minimum_parent_dir_size = ($this->params['multilanguage']) ? 2 : 1;
            if (count($this->file->getParents()) >= $minimum_parent_dir_size) {
                $this->title = $this->file->getParent()->getTitle();
            } else {
                $this->homepage = ($this->file->getName() === '_index');
                $this->title = $this->params['title'];
            }
        }

        $this->language = '';
        if ($this->params['multilanguage'] && count($this->file->getParents())) {
            reset($this->file->getParents());
            $language_dir = current($this->file->getParents());
            $this->language = $language_dir->name;
        }
    }

    private function getBreadcrumbTrail($parents, $multilanguage)
    {
        if ($multilanguage && !empty($parents)) {
            $parents = array_splice($parents, 1);
        }
        $breadcrumb_trail = array();
        if (!empty($parents)) {
            foreach ($parents as $node) {
                $breadcrumb_trail[$node->getTitle()] = $node->getUrl();
            }
        }
        return $breadcrumb_trail;
    }

    protected function generatePage()
    {
        $this->initialize();
        $params = $this->params;

        $entry_page = [];
        if ($params['request'] === $params['index_key']) {
            if ($params['multilanguage']) {
                foreach ($params['languages'] as $key => $name) {
                    $entry_page[$name] = $params['base_page'] . $params['entry_page'][$key]->getUrl();
                }
            } else {
                $entry_page['View Documentation'] = $params['base_page'] . $params['entry_page']->getUrl();
            }
        } elseif ($params['file_uri'] === 'index') {
            $entry_page[$params['entry_page']->getTitle()] = $params['base_page'] . $params['entry_page']->getUrl();
        }

        $page = [
            'entry_page' => $entry_page,
            'homepage' => $this->homepage,
            'title' => $this->file->getTitle(),
            'filename' => $this->file->getName(),
            'language' => $this->language,
            'path' => $this->file->getPath(),
            'modified_time' => filemtime($this->file->getPath()),
            'markdown' => $this->content,
            'request' => $params['request'],
            'content' => $this->convertPage($this->content),
            'breadcrumbs' => $params['breadcrumbs']
        ];

        if ($page['breadcrumbs']) {
            $page['breadcrumb_trail'] = $this->getBreadcrumbTrail($this->file->getParents(), $params['multilanguage']);
            $page['breadcrumb_separator'] = $params['breadcrumb_separator'];
        }

        $template = new Template($params['templates'], $params['theme']['templates']);
        return $template->render($this->homepage ? 'home' : 'content', ['page' => $page, 'params' => $params]);
    }
}
