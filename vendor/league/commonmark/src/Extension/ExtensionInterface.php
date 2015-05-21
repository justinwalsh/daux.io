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

interface ExtensionInterface
{
    /**
     * Returns a list of block parsers to add to the existing list
     *
     * @return BlockParserInterface[]
     */
    public function getBlockParsers();

    /**
     * Returns a list of block renderers to add to the existing list
     *
     * The list keys are the block class names which the corresponding value (renderer) will handle.
     *
     * @return BlockRendererInterface[]
     */
    public function getBlockRenderers();

    /**
     * Returns a list of inline parsers to add to the existing list
     *
     * @return InlineParserInterface[]
     */
    public function getInlineParsers();

    /**
     * Returns a list of inline processors to add to the existing list
     *
     * @return InlineProcessorInterface[]
     */
    public function getInlineProcessors();

    /**
     * Returns a list of inline renderers to add to the existing list
     *
     * The list keys are the inline class names which the corresponding value (renderer) will handle.
     *
     * @return InlineRendererInterface[]
     */
    public function getInlineRenderers();

    /**
     * Returns the name of the extension
     *
     * @return string
     */
    public function getName();
}
