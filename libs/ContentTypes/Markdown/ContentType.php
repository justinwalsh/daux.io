<?php namespace Todaymade\Daux\ContentTypes\Markdown;

use Todaymade\Daux\Config;

class ContentType implements \Todaymade\Daux\ContentTypes\ContentType
{
    /** @var Config */
    protected $config;

    /** @var CommonMarkConverter */
    protected $converter;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->converter = new CommonMarkConverter(['daux' => $config]);
    }

    /**
     * @return array
     */
    public function getExtensions()
    {
        return ['md', 'markdown'];
    }

    public function convert($html)
    {
        return $this->converter->convertToHtml($html);
    }
}
