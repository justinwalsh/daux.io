<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (http://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Block\Renderer;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Block\Element\Paragraph;
use League\CommonMark\HtmlElement;
use League\CommonMark\HtmlRendererInterface;

class ParagraphRenderer implements BlockRendererInterface
{
    /**
     * @param Paragraph $block
     * @param HtmlRendererInterface $htmlRenderer
     * @param bool $inTightList
     *
     * @return HtmlElement|string
     */
    public function render(AbstractBlock $block, HtmlRendererInterface $htmlRenderer, $inTightList = false)
    {
        if (!($block instanceof Paragraph)) {
            throw new \InvalidArgumentException('Incompatible block type: ' . get_class($block));
        }

        if ($inTightList) {
            return $htmlRenderer->renderInlines($block->getInlines());
        } else {
            return new HtmlElement('p', array(), $htmlRenderer->renderInlines($block->getInlines()));
        }
    }
}
