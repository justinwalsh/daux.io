<?php namespace Todaymade\Daux\Format\Base\CommonMark;

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

        $this->extendEnvironment($environment);

        $this->docParser = new DocParser($environment);
        $this->htmlRenderer = new HtmlRenderer($environment);
    }

    protected function getLinkRenderer(Environment $environment)
    {
      return new LinkRenderer($environment->getConfig('daux'));
    }

    protected function extendEnvironment(Environment $environment)
    {
        $environment->addInlineRenderer('Link', $this->getLinkRenderer($environment));
    }
}
