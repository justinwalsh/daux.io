<?php namespace Todaymade\Daux\Format\Confluence\CommonMark;

use League\CommonMark\HtmlElement;
use League\CommonMark\HtmlRendererInterface;
use League\CommonMark\Inline\Element\AbstractInline;
use League\CommonMark\Inline\Element\Link;

class LinkRenderer extends \Todaymade\Daux\Format\Base\CommonMark\LinkRenderer
{
    /**
     * @param Link $inline
     * @param HtmlRendererInterface $htmlRenderer
     *
     * @return HtmlElement
     */
    public function render(AbstractInline $inline, HtmlRendererInterface $htmlRenderer)
    {
        // Default handling
        $element = parent::render($inline, $htmlRenderer);
        $url = $inline->getUrl();
        if (empty($url) || $url[0] != '!') {
            return $element;
        }

        //Internal links
        $file = $this->resolveInternalFile(ltrim($url, "!"));

        $link_props = [
            'ri:content-title' => trim($this->daux['confluence']['prefix']) . " " . $file->getTitle(),
            'ri:space-key' => $this->daux['confluence']['space_id']
        ];

        $page = strval(new HtmlElement('ri:page', $link_props, '', true));
        $children = $htmlRenderer->renderInlines($inline->getChildren());
        if (strpos($children, "<") !== false) {
            $children = '<ac:link-body>' . $children . '</ac:link-body>';
        } else {
            $children = '<ac:plain-text-link-body><![CDATA[' . $children . ']]></ac:plain-text-link-body>';
        }

        return new HtmlElement('ac:link', [], $page . $children);
    }
}
