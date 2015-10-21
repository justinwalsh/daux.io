<?php namespace Todaymade\Daux\ContentTypes\Markdown;

use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\HtmlRenderer;
use Webuni\CommonMark\TableExtension\TableExtension;

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
        $environment->addExtension(new TableExtension());

        $this->extendEnvironment($environment);

        if (array_key_exists('processor_instance', $config['daux'])) {
            $config['daux']['processor_instance']->extendCommonMarkEnvironment($environment);
        }

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
