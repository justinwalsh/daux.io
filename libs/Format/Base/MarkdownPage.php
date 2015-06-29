<?php namespace Todaymade\Daux\Format\Base;

use Todaymade\Daux\Config;
use Todaymade\Daux\Format\Base\CommonMark\CommonMarkConverter;
use Todaymade\Daux\Tree\Content;

abstract class MarkdownPage extends SimplePage
{
    /**
     * @var Content
     */
    protected $file;

    /**
     * @var Config
     */
    protected $params;

    public function __construct($title, $content)
    {
        $this->initializePage($title, $content);
    }

    public function setFile(Content $file)
    {
        $this->file = $file;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setParams(Config $params)
    {
        $this->params = $params;
    }

    protected function getMarkdownConverter()
    {
        return new CommonMarkConverter(['daux' => $this->params]);
    }

    protected function convertPage($content)
    {
        return $this->getMarkdownConverter()->convertToHtml($content);
    }

    protected function generatePage()
    {
        return $this->convertPage($this->content);
    }

    public static function fromFile(Content $file, $params)
    {
        $page = new static($file->title, file_get_contents($file->getPath()));
        $page->setFile($file);
        $page->setParams($params);

        return $page;
    }
}
