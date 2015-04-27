<?php namespace Todaymade\Daux;

use Todaymade\Daux\Tree\Content;

class MarkdownPage extends SimplePage
{
    private $file;
    private $params;
    private $language;
    private $homepage;

    public function __construct()
    {
    }

    // For Future Expansion
    public static function fromFile($file, $params)
    {
        $instance = new self();
        $instance->initialize($file, $params);
        return $instance;
    }

    private function initialize(Content $file, $params)
    {
        $this->file = $file;
        $this->params = $params;
        $this->title = $file->title;

        $this->homepage = false;
        if ($this->title === 'index') {
            $minimum_parent_dir_size = ($params['multilanguage']) ? 2 : 1;
            if (count($file->getParents()) >= $minimum_parent_dir_size) {
                $parents = $file->getParents();
                $this->title = end($parents)->getTitle();
            } else {
                $this->homepage = ($this->file->getName() === '_index');
                $this->title = $params['title'];
            }
        }

        $this->language = '';
        if ($params['multilanguage'] && count($file->getParents())) {
            reset($file->getParents());
            $language_dir = current($file->getParents());
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

    public function getContent()
    {
        if (is_null($this->html)) {
            $this->content = file_get_contents($this->file->getPath());
            $this->html = $this->generatePage();
        }

        return $this->html;
    }

    private function generatePage()
    {
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
            $entry_page[$params['entry_page']->title] = $params['base_page'] . $params['entry_page']->getUrl();
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
            'content' => (new \Parsedown())->text($this->content),
            'breadcrumbs' => $params['breadcrumbs']
        ];

        if ($page['breadcrumbs']) {
            $page['breadcrumb_trail'] = $this->getBreadcrumbTrail($this->file->getParents(), $params['multilanguage']);
            $page['breadcrumb_separator'] = $params['breadcrumb_separator'];
        }

        $template = new Template($params['templates'], $params['theme']['templates']);
        return $template->render($this->homepage? 'home' : 'content', ['page' => $page, 'params' => $params]);
    }
}
