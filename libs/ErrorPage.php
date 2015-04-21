<?php namespace Todaymade\Daux;

class ErrorPage extends SimplePage
{
    const NORMAL_ERROR_TYPE = 'NORMAL_ERROR';
    const MISSING_PAGE_ERROR_TYPE = 'MISSING_PAGE_ERROR';
    const FATAL_ERROR_TYPE = 'FATAL_ERROR';

    private $params;
    private $type;
    private static $template;

    public function __construct($title, $content, $params) {
        parent::__construct($title, $content);
        $this->params = $params;
        $this->type = $params['error_type'];
    }

    public function display() {
        http_response_code($this->type === static::MISSING_PAGE_ERROR_TYPE ? 404 : 500);
        parent::display();
    }

    public function get_page_content() {
        if ($this->type !== static::FATAL_ERROR_TYPE && is_null(static::$template)) {
            include_once($this->params['theme']['error-template']);
            static::$template = new Template();
        }

        if (is_null($this->html)) {
            $this->html = $this->generate_page();
        }

        return $this->html;
    }

    public function generate_page() {
        if ($this->type === static::FATAL_ERROR_TYPE) return $this->content;
        $params = $this->params;
        $page['title'] = $this->title;
        $page['theme'] = $params['theme'];
        $page['content'] = $this->content;
        $page['google_analytics'] = $params['google_analytics'];
        $page['piwik_analytics'] = $params['piwik_analytics'];

        return static::$template->get_content($page, $params);
    }
}
