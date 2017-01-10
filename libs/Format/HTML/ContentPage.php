<?php namespace Todaymade\Daux\Format\HTML;

use Todaymade\Daux\Tree\Root;

class ContentPage extends \Todaymade\Daux\Format\Base\ContentPage
{
    private $language;
    private $homepage;

    private function isHomepage()
    {
        // If we don't have the auto_landing parameter, we don't want any homepage
        if (array_key_exists('auto_landing', $this->params['html']) && !$this->params['html']['auto_landing']) {
            return false;
        }

        // If the current page isn't the index, no chance it is the landing page
        if ($this->file->getParent()->getIndexPage() != $this->file) {
            return false;
        }

        // If the direct parent is root, this is the homage
        return $this->file->getParent() instanceof Root;
    }

    private function initialize()
    {
        $this->homepage = $this->isHomepage();

        $this->language = '';
        if ($this->params->isMultilanguage() && count($this->file->getParents())) {
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
        $breadcrumb_trail = [];
        if (!empty($parents)) {
            foreach ($parents as $node) {
                $page = $node->getIndexPage() ?: $node->getFirstPage();
                $breadcrumb_trail[$node->getTitle()] = $page ? $page->getUrl() : '';
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
            if ($params->isMultilanguage()) {
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
            'relative_path' => $this->file->getRelativePath(),
            'modified_time' => filemtime($this->file->getPath()),
            'markdown' => $this->content,
            'request' => $params['request'],
            'content' => $this->getPureContent(),
            'breadcrumbs' => $params['html']['breadcrumbs'],
            'prev' => $this->file->getPrevious(),
            'next' => $this->file->getNext(),
        ];

        if ($page['breadcrumbs']) {
            $page['breadcrumb_trail'] = $this->getBreadcrumbTrail($this->file->getParents(), $params->isMultilanguage());
            $page['breadcrumb_separator'] = $params['html']['breadcrumb_separator'];
        }

        $context = ['page' => $page, 'params' => $params];

        $template = new Template($params['templates'], $params['theme']['templates']);

        return $template->render($this->homepage ? 'theme::home' : 'theme::content', $context);
    }
}
