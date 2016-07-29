<?php namespace Todaymade\Daux\Format\Confluence\ContentTypes\Markdown;

use League\CommonMark\ElementRendererInterface;
use League\CommonMark\HtmlElement;
use League\CommonMark\Inline\Element\AbstractInline;
use League\CommonMark\Inline\Element\Image;

class ImageRenderer extends \League\CommonMark\Inline\Renderer\ImageRenderer
{
    /**
     * @param Image                    $inline
     * @param ElementRendererInterface $htmlRenderer
     *
     * @return HtmlElement
     */
    public function render(AbstractInline $inline, ElementRendererInterface $htmlRenderer)
    {
        if (!($inline instanceof Image)) {
            throw new \InvalidArgumentException('Incompatible inline type: ' . get_class($inline));
        }

        // External Images need special handling
        if (strpos($inline->getUrl(), 'http') === 0) {
            return new HtmlElement(
                'ac:image',
                [],
                new HtmlElement('ri:url', ['ri:value' => $inline->getUrl()])
            );
        }

        return parent::render($inline, $htmlRenderer);
    }
}
