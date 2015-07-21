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
use League\CommonMark\Block\Element\Document;
use League\CommonMark\HtmlRendererInterface;

class DocumentRenderer implements BlockRendererInterface
{
    /**
     * @param AbstractBlock|Document $block
     * @param HtmlRendererInterface $htmlRenderer
     * @param bool $inTightList
     *
     * @return string
     */
    public function render(AbstractBlock $block, HtmlRendererInterface $htmlRenderer, $inTightList = false)
    {
        if (!($block instanceof Document)) {
            throw new \InvalidArgumentException('Incompatible block type: ' . get_class($block));
        }

        $wholeDoc = $htmlRenderer->renderBlocks($block->getChildren());

        return $wholeDoc === '' ? '' : $wholeDoc . "\n";
    }
}
