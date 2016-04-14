<?php namespace Todaymade\Daux\Format\HTML\ContentTypes\Markdown;

use League\CommonMark\Environment;
use Todaymade\Daux\Config;

class CommonMarkConverter extends \Todaymade\Daux\ContentTypes\Markdown\CommonMarkConverter
{
    protected function extendEnvironment(Environment $environment, Config $config)
    {
        parent::extendEnvironment($environment, $config);

        $environment->addDocumentProcessor(new TOC\Processor($config));
        $environment->addBlockRenderer('Todaymade\Daux\ContentTypes\Markdown\TableOfContents', new TOC\Renderer());
    }
}
