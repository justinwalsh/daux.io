<?php namespace Todaymade\Daux\Format\Confluence\ContentTypes\Markdown;

use League\CommonMark\Environment;
use Todaymade\Daux\Config;

class CommonMarkConverter extends \Todaymade\Daux\ContentTypes\Markdown\CommonMarkConverter
{
    protected function getLinkRenderer(Environment $environment)
    {
        return new LinkRenderer($environment->getConfig('daux'));
    }

    protected function extendEnvironment(Environment $environment, Config $config)
    {
        parent::extendEnvironment($environment, $config);

        $environment->addBlockRenderer('Todaymade\Daux\ContentTypes\Markdown\TableOfContents', new TOCRenderer());

        //Add code renderer
        $environment->addBlockRenderer('FencedCode', new FencedCodeRenderer());
        $environment->addBlockRenderer('IndentedCode', new IndentedCodeRenderer());

        $environment->addInlineRenderer('Image', new ImageRenderer());
    }
}
