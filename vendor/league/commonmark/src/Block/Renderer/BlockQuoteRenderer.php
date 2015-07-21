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
use League\CommonMark\Block\Element\BlockQuote;
use League\CommonMark\HtmlElement;
use League\CommonMark\HtmlRendererInterface;

class BlockQuoteRenderer implements BlockRendererInterface
{
    /**
     * @param BlockQuote $block
     * @param HtmlRendererInterface $htmlRenderer
     * @param bool $inTightList
     *
     * @return HtmlElement
     */
    public function render(AbstractBlock $block, HtmlRendererInterface $htmlRenderer, $inTightList = false)
    {
        if (!($block instanceof BlockQuote)) {
            throw new \InvalidArgumentException('Incompatible block type: ' . get_class($block));
        }

        $filling = $htmlRenderer->renderBlocks($block->getChildren());
        if ($filling === '') {
            return new HtmlElement('blockquote', array(), $htmlRenderer->getOption('inner_separator', "\n"));
        }

        return new HtmlElement(
            'blockquote',
            array(),
            $htmlRenderer->getOption('inner_separator', "\n") . $filling . $htmlRenderer->getOption('inner_separator', "\n")
        );
    }
}
