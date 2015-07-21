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
use League\CommonMark\Block\Element\FencedCode;
use League\CommonMark\HtmlElement;
use League\CommonMark\HtmlRendererInterface;

class FencedCodeRenderer implements BlockRendererInterface
{
    /**
     * @param FencedCode $block
     * @param HtmlRendererInterface $htmlRenderer
     * @param bool $inTightList
     *
     * @return HtmlElement
     */
    public function render(AbstractBlock $block, HtmlRendererInterface $htmlRenderer, $inTightList = false)
    {
        if (!($block instanceof FencedCode)) {
            throw new \InvalidArgumentException('Incompatible block type: ' . get_class($block));
        }

        $infoWords = $block->getInfoWords();
        $attr = count($infoWords) === 0 || strlen(
            $infoWords[0]
        ) === 0 ? array() : array('class' => 'language-' . $htmlRenderer->escape($infoWords[0], true));

        return new HtmlElement(
            'pre',
            array(),
            new HtmlElement('code', $attr, $htmlRenderer->escape($block->getStringContent()))
        );
    }
}
