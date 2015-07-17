<?php namespace Todaymade\Daux\Format\Base\CommonMark;

use League\CommonMark\HtmlElement;
use League\CommonMark\HtmlRendererInterface;
use League\CommonMark\Inline\Element\AbstractInline;
use League\CommonMark\Inline\Element\Link;
use Todaymade\Daux\Config;
use Todaymade\Daux\DauxHelper;
use Todaymade\Daux\Exception;
use Todaymade\Daux\Tree\Entry;

class LinkRenderer extends \League\CommonMark\Inline\Renderer\LinkRenderer
{
    /**
     * @var Config
     */
    protected $daux;

    public function __construct($daux)
    {
        $this->daux = $daux;
    }

    /**
     * @param string $url
     * @return Entry
     * @throws Exception
     */
    protected function resolveInternalFile($url)
    {
        $file = DauxHelper::getFile($this->daux['tree'], $url);
        if ($file) {
            return $file;
        }

        $file = DauxHelper::getFile($this->daux['tree'], $url . '.html');
        if ($file) {
            return $file;
        }

        throw new Exception("Could not locate file '$url'");
    }

    /**
     * @param Link $inline
     * @param HtmlRendererInterface $htmlRenderer
     *
     * @return HtmlElement
     */
    public function render(AbstractInline $inline, HtmlRendererInterface $htmlRenderer)
    {
        $element = parent::render($inline, $htmlRenderer);

        $url = $inline->getUrl();
        if (!empty($url) && $url[0] == '!') {
            $file = $this->resolveInternalFile(ltrim($url, "!"));

            $element->setAttribute('href', $this->daux['base_url'] . $file->getUrl());
        }

        return $element;
    }
}
