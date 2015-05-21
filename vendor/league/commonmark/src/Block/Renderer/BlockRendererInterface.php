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
use League\CommonMark\HtmlElement;
use League\CommonMark\HtmlRendererInterface;

interface BlockRendererInterface
{
    /**
     * @param AbstractBlock $block
     * @param HtmlRendererInterface $htmlRenderer
     * @param bool $inTightList
     *
     * @return HtmlElement|string
     */
    public function render(AbstractBlock $block, HtmlRendererInterface $htmlRenderer, $inTightList = false);
}
