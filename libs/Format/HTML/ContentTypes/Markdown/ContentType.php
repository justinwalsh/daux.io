<?php namespace Todaymade\Daux\Format\HTML\ContentTypes\Markdown;

use Todaymade\Daux\Config;

class ContentType extends \Todaymade\Daux\ContentTypes\Markdown\ContentType
{
    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->converter = new CommonMarkConverter(['daux' => $config]);
    }
}
