<?php namespace Todaymade\Daux;

class SimplePage implements Page
{
    protected $title;
    protected $content;
    protected $html = null;

    public function __construct($title, $content) {
        $this->initialize_page($title, $content);
    }

    public function initialize_page($title, $content) {
        $this->title = $title;
        $this->content = $content;
    }

    public function  display() {
        header('Content-type: text/html; charset=utf-8');
        echo $this->get_page_content();
    }

    public function get_page_content() {
        if (is_null($this->html)) {
            $this->html = $this->generate_page();
        }

        return $this->html;
    }

    private function generate_page() {
        return $this->content;
    }
}
