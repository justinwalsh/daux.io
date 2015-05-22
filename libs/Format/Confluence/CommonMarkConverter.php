<?php namespace Todaymade\Daux\Format\Confluence;

use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\HtmlRenderer;

class CommonMarkConverter extends \League\CommonMark\CommonMarkConverter
{
    /**
     * Create a new commonmark converter instance.
     *
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        $environment = Environment::createCommonMarkEnvironment();
        $environment->mergeConfig($config);

        //Add code renderer
        $environment->addBlockRenderer('FencedCode', new FencedCodeRenderer());
        $environment->addBlockRenderer('IndentedCode', new IndentedCodeRenderer());

        $this->docParser = new DocParser($environment);
        $this->htmlRenderer = new HtmlRenderer($environment);
    }
}
