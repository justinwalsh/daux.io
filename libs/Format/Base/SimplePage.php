<?php namespace Todaymade\Daux\Format\Base;

abstract class SimplePage implements Page
{
    protected $title;
    protected $content;
    protected $generated = null;

    public function __construct($title, $content)
    {
        $this->initializePage($title, $content);
    }

    public function getPureContent()
    {
        return $this->content;
    }

    public function getContent()
    {
        if (is_null($this->generated)) {
            $this->generated = $this->generatePage();
        }

        return $this->generated;
    }

    protected function initializePage($title, $content)
    {
        $this->title = $title;
        $this->content = $content;
    }

    protected function generatePage()
    {
        return $this->content;
    }
}
