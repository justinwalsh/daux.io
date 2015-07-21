<?php namespace Todaymade\Daux;

class SimplePage implements Page
{
    protected $title;
    protected $content;
    protected $html = null;

    public function __construct($title, $content)
    {
        $this->initializePage($title, $content);
    }

    public function display()
    {
        header('Content-type: text/html; charset=utf-8');
        echo $this->getContent();
    }

    public function getContent()
    {
        if (is_null($this->html)) {
            $this->html = $this->generatePage();
        }

        return $this->html;
    }

    private function initializePage($title, $content)
    {
        $this->title = $title;
        $this->content = $content;
    }

    private function generatePage()
    {
        return $this->content;
    }
}
