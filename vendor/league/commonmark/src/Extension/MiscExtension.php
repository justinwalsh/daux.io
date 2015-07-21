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

class MiscExtension implements ExtensionInterface
{
    /**
     * @var BlockParserInterface[]
     */
    protected $blockParsers = array();

    /**
     * @var BlockRendererInterface[]
     */
    protected $blockRenderers = array();

    /**
     * @var InlineParserInterface[]
     */
    protected $inlineParsers = array();

    /**
     * @var InlineProcessorInterface[]
     */
    protected $inlineProcessers = array();

    /**
     * @var InlineRendererInterface[]
     */
    protected $inlineRenderers = array();

    /**
     * Returns a list of block parsers to add to the existing list
     *
     * @return BlockParserInterface[]
     */
    public function getBlockParsers()
    {
        return $this->blockParsers;
    }

    /**
     * @param BlockParserInterface $blockParser
     *
     * @return $this
     */
    public function addBlockParser(BlockParserInterface $blockParser)
    {
        $this->blockParsers[] = $blockParser;

        return $this;
    }

    /**
     * Returns a list of block renderers to add to the existing list
     *
     * The list keys are the block class names which the corresponding value (renderer) will handle.
     *
     * @return BlockRendererInterface[]
     */
    public function getBlockRenderers()
    {
        return $this->blockRenderers;
    }

    /**
     * @param string $blockClass
     * @param BlockRendererInterface $blockRenderer
     *
     * @return $this
     */
    public function addBlockRenderer($blockClass, BlockRendererInterface $blockRenderer)
    {
        if (class_exists('League\CommonMark\Block\Element\\' . $blockClass)) {
            $blockClass = 'League\CommonMark\Block\Element\\' . $blockClass;
        }

        $this->blockRenderers[$blockClass] = $blockRenderer;
    }

    /**
     * Returns a list of inline parsers to add to the existing list
     *
     * @return InlineParserInterface[]
     */
    public function getInlineParsers()
    {
        return $this->inlineParsers;
    }

    /**
     * @param InlineParserInterface $inlineParser
     *
     * @return $this
     */
    public function addInlineParser(InlineParserInterface $inlineParser)
    {
        $this->inlineParsers[] = $inlineParser;
    }

    /**
     * Returns a list of inline processors to add to the existing list
     *
     * @return InlineProcessorInterface[]
     */
    public function getInlineProcessors()
    {
        return $this->inlineProcessers;
    }

    /**
     * @param InlineProcessorInterface $inlineProcessor
     *
     * @return $this
     */
    public function addInlineProcessor(InlineProcessorInterface $inlineProcessor)
    {
        $this->inlineProcessers[] = $inlineProcessor;

        return $this;
    }

    /**
     * Returns a list of inline renderers to add to the existing list
     *
     * The list keys are the inline class names which the corresponding value (renderer) will handle.
     *
     * @return InlineRendererInterface[]
     */
    public function getInlineRenderers()
    {
        return $this->inlineRenderers;
    }

    /**
     * @param string $inlineClass
     * @param InlineRendererInterface $inlineRenderer
     *
     * @return $this
     */
    public function addInlineRenderer($inlineClass, InlineRendererInterface $inlineRenderer)
    {
        if (class_exists('League\CommonMark\Inline\Element\\' . $inlineClass)) {
            $inlineClass = 'League\CommonMark\Inline\Element\\' . $inlineClass;
        }

        $this->inlineRenderers[$inlineClass] = $inlineRenderer;

        return $this;
    }

    /**
     * Returns the name of the extension
     *
     * @return string
     */
    public function getName()
    {
        return 'misc';
    }
}
