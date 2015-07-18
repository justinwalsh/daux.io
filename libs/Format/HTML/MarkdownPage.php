<?php namespace Todaymade\Daux\Format\HTML;

use Todaymade\Daux\Daux;
use Todaymade\Daux\Tree\Root;

class MarkdownPage extends \Todaymade\Daux\Format\Base\MarkdownPage
{
    private $language;
    private $homepage;

    private function initialize()
    {
        $this->homepage = false;
        if ($this->file->getParent()->getIndexPage() == $this->file) {
            if ($this->params['multilanguage']) {
                if ($this->file->getParent()->getParent() instanceof Root) {
                    $this->homepage = true;
                }
            } elseif ($this->file->getParent() instanceof Root) {
                $this->homepage = true;
            }
        }

        $this->language = '';
        if ($this->params['multilanguage'] && count($this->file->getParents())) {
            $language_dir = $this->file->getParents()[0];
            $this->language = $language_dir->getName();
        }
    }

    /**
     * @param \Todaymade\Daux\Tree\Directory[] $parents
     * @param bool $multilanguage
     * @return array
     */
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
        if ($this->homepage) {
            if ($params['multilanguage']) {
                foreach ($params['languages'] as $key => $name) {
                    $entry_page[$name] = $params['base_page'] . $params['entry_page'][$key]->getUrl();
                }
            } else {
                $entry_page['View Documentation'] = $params['base_page'] . $params['entry_page']->getUrl();
            }
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
