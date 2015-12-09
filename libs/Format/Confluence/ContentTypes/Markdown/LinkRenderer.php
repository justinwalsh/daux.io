<?php namespace Todaymade\Daux\Format\Confluence\ContentTypes\Markdown;

use League\CommonMark\HtmlElement;
use League\CommonMark\ElementRendererInterface;
use League\CommonMark\Inline\Element\AbstractInline;
use League\CommonMark\Inline\Element\Link;

class LinkRenderer extends \Todaymade\Daux\ContentTypes\Markdown\LinkRenderer
{
    /**
     * @param Link $inline
     * @param ElementRendererInterface $htmlRenderer
     *
     * @return HtmlElement
     */
    public function render(AbstractInline $inline, ElementRendererInterface $htmlRenderer)
    {
        // This can't be in the method type as
        // the method is an abstract and should
        // have the same interface
        if (!$inline instanceof Link) {
            throw new \RuntimeException(
                "Wrong type passed to " . __CLASS__ . "::" . __METHOD__ .
                " the expected type was 'League\\CommonMark\\Inline\\Element\\Link' but '" .
                get_class($inline) . "' was provided"
            );
        }

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
        $children = $htmlRenderer->renderInlines($inline->children());
        if (strpos($children, "<") !== false) {
            $children = '<ac:link-body>' . $children . '</ac:link-body>';
        } else {
            $children = '<ac:plain-text-link-body><![CDATA[' . $children . ']]></ac:plain-text-link-body>';
        }

        return new HtmlElement('ac:link', [], $page . $children);
    }
}
