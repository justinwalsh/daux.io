<?php namespace Todaymade\Daux\Format\HTML\ContentTypes\Markdown\TOC;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Block\Renderer\BlockRendererInterface;
use League\CommonMark\ElementRendererInterface;

class Renderer implements BlockRendererInterface
{
    public function render(AbstractBlock $block, ElementRendererInterface $htmlRenderer, $inTightList = false)
    {
        return $htmlRenderer->renderBlocks($block->children());
    }
}
