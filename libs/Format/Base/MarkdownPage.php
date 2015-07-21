<?php namespace Todaymade\Daux\Format\Base;

use League\CommonMark\CommonMarkConverter;
use Todaymade\Daux\Config;
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

    /**
     * @var CommonMarkConverter
     */
    protected $converter;

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
        return $this->converter;
    }

    protected function convertPage($content)
    {
        return $this->getMarkdownConverter()->convertToHtml($content);
    }

    protected function generatePage()
    {
        return $this->convertPage($this->content);
    }

    public static function fromFile(Content $file, $params, CommonMarkConverter $converter)
    {
        $page = new static($file->getTitle(), $file->getContent());
        $page->setFile($file);
        $page->setParams($params);
        $page->converter = $converter;

        return $page;
    }
}
