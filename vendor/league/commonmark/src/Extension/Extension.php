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

namespace League\CommonMark\Extension;

use League\CommonMark\Block\Parser\BlockParserInterface;
use League\CommonMark\Block\Renderer\BlockRendererInterface;
use League\CommonMark\Inline\Parser\InlineParserInterface;
use League\CommonMark\Inline\Processor\InlineProcessorInterface;
use League\CommonMark\Inline\Renderer\InlineRendererInterface;

abstract class Extension implements ExtensionInterface
{
    /**
     * @return BlockParserInterface[]
     */
    public function getBlockParsers()
    {
        return array();
    }

    /**
     * @return BlockRendererInterface[]
     */
    public function getBlockRenderers()
    {
        return array();
    }

    /**
     * @return InlineParserInterface[]
     */
    public function getInlineParsers()
    {
        return array();
    }

    /**
     * @return InlineProcessorInterface[]
     */
    public function getInlineProcessors()
    {
        return array();
    }

    /**
     * @return InlineRendererInterface[]
     */
    public function getInlineRenderers()
    {
        return array();
    }
}
