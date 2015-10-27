<?php namespace Todaymade\Daux\Format\Confluence\ContentTypes\Markdown;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Block\Element\IndentedCode;
use League\CommonMark\Block\Renderer\BlockRendererInterface;
use League\CommonMark\ElementRendererInterface;
use League\CommonMark\HtmlElement;

class IndentedCodeRenderer implements BlockRendererInterface
{
    /**
     * @param AbstractBlock $block
     * @param HtmlRendererInterface $htmlRenderer
     * @param bool $inTightList
     *
     * @return HtmlElement
     */
    public function render(AbstractBlock $block, ElementRendererInterface $htmlRenderer, $inTightList = false)
    {
        if (!($block instanceof IndentedCode)) {
            throw new \InvalidArgumentException('Incompatible block type: ' . get_class($block));
        }

        return new HtmlElement(
            'ac:structured-macro',
            ['ac:name' => 'code'],
            new HtmlElement('ac:plain-text-body', [], '<![CDATA[' . $block->getStringContent() . ']]>')
        );
    }
}
