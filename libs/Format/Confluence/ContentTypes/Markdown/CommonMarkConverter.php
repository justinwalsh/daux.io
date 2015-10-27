<?php namespace Todaymade\Daux\Format\Confluence\ContentTypes\Markdown;

use League\CommonMark\Environment;

class CommonMarkConverter extends \Todaymade\Daux\ContentTypes\Markdown\CommonMarkConverter
{
    protected function getLinkRenderer(Environment $environment)
    {
        return new LinkRenderer($environment->getConfig('daux'));
    }

    protected function extendEnvironment(Environment $environment)
    {
        parent::extendEnvironment($environment);

        //Add code renderer
        $environment->addBlockRenderer('FencedCode', new FencedCodeRenderer());
        $environment->addBlockRenderer('IndentedCode', new IndentedCodeRenderer());

        $environment->addInlineRenderer('Image', new ImageRenderer());
    }
}
