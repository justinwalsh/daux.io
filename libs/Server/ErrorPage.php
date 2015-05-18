<?php namespace Todaymade\Daux\Server;

use Todaymade\Daux\Format\HTML\SimplePage;
use Todaymade\Daux\Format\HTML\Template;

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

    protected function generatePage()
    {
        $params = $this->params;
        $page['title'] = $this->title;
        $page['content'] = $this->content;

        $template = new Template($params['templates'], $params['theme']['templates']);
        return $template->render('error', ['page' => $page, 'params' => $params]);
    }
}
