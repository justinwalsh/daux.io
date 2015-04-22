<?php namespace Todaymade\Daux;

use Todaymade\Daux\Tree\Content;

class MarkdownPage extends SimplePage
{
    private $file;
    private $params;
    private $language;
    private $homepage;
    private $breadcrumb_trail;
    private static $template;

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

        if ($this->title === 'index') {
            $this->homepage = ($this->file->getName() === '_index');
            $minimum_parent_dir_size = ($params['multilanguage']) ? 2 : 1;
            if (count($file->getParents()) >= $minimum_parent_dir_size) {
                $parents = $file->getParents();
                $parent = end($parents);
                $this->title = $parent->getTitle();
            } else {
                $this->title = $params['title'];
            }
        } else {
            $this->homepage = false;
        }
        if ($params['breadcrumbs']) {
            $this->breadcrumb_trail = $this->buildBreadcrumbTrail($file->getParents(), $params['multilanguage']);
        }
        $this->language = '';
        if ($params['multilanguage'] && count($file->getParents())) {
            reset($file->getParents());
            $language_dir = current($file->getParents());
            $this->language = $language_dir->name;
        }
        if (is_null(static::$template)) {
            include_once($params['theme']['template']);
            static::$template = new Template();
        }
    }

    private function buildBreadcrumbTrail($parents, $multilanguage)
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
                    $entry_page[utf8_encode($name)] = utf8_encode($params['base_page'] . $params['entry_page'][$key]->getUrl());
                }
            } else {
                $entry_page['View Documentation'] = utf8_encode($params['base_page'] . $params['entry_page']->getUri());
            }
        } elseif ($params['file_uri'] === 'index') {
            $entry_page[utf8_encode($params['entry_page']->title)] = utf8_encode($params['base_page'] . $params['entry_page']->getUrl());
        }

        $page['entry_page'] = $entry_page;
        $page['homepage'] = $this->homepage;
        $page['title'] = $this->file->getTitle();
        $page['tagline'] = $params['tagline'];
        $page['author'] = $params['author'];
        $page['filename'] = $this->file->getName();
        if ($page['breadcrumbs'] = $params['breadcrumbs']) {
            $page['breadcrumb_trail'] = $this->breadcrumb_trail;
            $page['breadcrumb_separator'] = $params['breadcrumb_separator'];
        }
        $page['language'] = $this->language;
        $page['path'] = $this->file->getPath();
        $page['request'] = utf8_encode($params['request']);
        $page['theme'] = $params['theme'];
        $page['modified_time'] = filemtime($this->file->getPath());
        $page['markdown'] = $this->content;
        $page['file_editor'] = $params['file_editor'];
        $page['google_analytics'] = $params['google_analytics'];
        $page['piwik_analytics'] = $params['piwik_analytics'];

        $Parsedown = new \Parsedown();
        $page['content'] = $Parsedown->text($this->content);

        return static::$template->get_content($page, $params);
    }
}
