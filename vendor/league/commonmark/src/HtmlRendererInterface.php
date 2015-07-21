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

namespace League\CommonMark;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Inline\Element\AbstractInline;

/**
 * Renders a parsed AST to HTML
 */
interface HtmlRendererInterface
{
    /**
     * @param string $option
     * @param mixed|null $default
     *
     * @return mixed|null
     */
    public function getOption($option, $default = null);

    /**
     * @param string $string
     * @param bool   $preserveEntities
     *
     * @return string
     */
    public function escape($string, $preserveEntities = false);

    /**
     * @param AbstractInline[] $inlines
     *
     * @return string
     */
    public function renderInlines($inlines);

    /**
     * @param AbstractBlock $block
     * @param bool         $inTightList
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    public function renderBlock(AbstractBlock $block, $inTightList = false);

    /**
     * @param AbstractBlock[] $blocks
     * @param bool            $inTightList
     *
     * @return string
     */
    public function renderBlocks($blocks, $inTightList = false);
}
