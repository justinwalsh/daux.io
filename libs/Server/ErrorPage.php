<?php namespace Todaymade\Daux\Server;

use Todaymade\Daux\SimplePage;
use Todaymade\Daux\Template;

class ErrorPage extends SimplePage
{
    const NORMAL_ERROR_TYPE = 'NORMAL_ERROR';
    const MISSING_PAGE_ERROR_TYPE = 'MISSING_PAGE_ERROR';
    const FATAL_ERROR_TYPE = 'FATAL_ERROR';

    private $params;

    public function __construct($title, $content, $params)
    {
        parent::__construct($title, $content);
        $this->params = $params;
    }

    public function display()
    {
        http_response_code(404);
        parent::display();
    }

    public function getContent()
    {
        if (is_null($this->html)) {
            $this->html = $this->generatePage();
        }

        return $this->html;
    }

    private function generatePage()
    {
        $params = $this->params;
        $page['title'] = $this->title;
        $page['content'] = $this->content;

        $template = new Template($params['templates'], $params['theme']['templates']);
        return $template->render('error', ['page' => $page, 'params' => $params]);
    }
}
