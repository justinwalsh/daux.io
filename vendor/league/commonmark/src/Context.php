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
use League\CommonMark\Block\Element\Document;

/**
 * Parses Markdown into an AST
 */
class Context implements ContextInterface
{
    /**
     * @var Environment
     */
    protected $environment;

    /**
     * @var AbstractBlock
     */
    protected $doc;

    /**
     * @var AbstractBlock|null
     */
    protected $tip;

    /**
     * @var AbstractBlock
     */
    protected $container;

    /**
     * @var int
     */
    protected $lineNumber;

    /**
     * @var string
     */
    protected $line;

    /**
     * @var UnmatchedBlockCloser
     */
    protected $blockCloser;

    /**
     * @var bool
     */
    protected $blocksParsed = false;

    protected $referenceParser;

    public function __construct(Document $document, Environment $environment)
    {
        $this->doc = $document;
        $this->tip = $this->doc;
        $this->container = $this->doc;

        $this->environment = $environment;

        $this->referenceParser = new ReferenceParser($document->getReferenceMap());

        $this->blockCloser = new UnmatchedBlockCloser($this);
    }

    /**
     * @param string $line
     */
    public function setNextLine($line)
    {
        ++$this->lineNumber;
        $this->line = $line;
    }

    /**
     * @param AbstractBlock $newBlock
     *
     * @return AbstractBlock
     */
    protected function addChild(AbstractBlock $newBlock)
    {
        $this->getBlockCloser()->closeUnmatchedBlocks();
        $newBlock->setStartLine($this->lineNumber);
        while (!$this->tip->canContain($newBlock)) {
            $this->tip->finalize($this);
        }

        $this->tip->addChild($newBlock);
        $this->tip = $newBlock;
        $this->container = $newBlock;

        return $newBlock;
    }

    /**
     * @return Document
     */
    public function getDocument()
    {
        return $this->doc;
    }

    /**
     * @return AbstractBlock|null
     */
    public function getTip()
    {
        return $this->tip;
    }

    /**
     * @param AbstractBlock|null $block
     *
     * @return $this
     */
    public function setTip(AbstractBlock $block = null)
    {
        $this->tip = $block;

        return $this;
    }

    /**
     * @return int
     */
    public function getLineNumber()
    {
        return $this->lineNumber;
    }

    /**
     * @return string
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * Finalize and close any unmatched blocks
     *
     * @return UnmatchedBlockCloser
     */
    public function getBlockCloser()
    {
        return $this->blockCloser;
    }

    /**
     * @return AbstractBlock
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param AbstractBlock $container
     *
     * @return $this
     */
    public function setContainer($container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * @param AbstractBlock $block
     *
     * @return AbstractBlock
     */
    public function addBlock(AbstractBlock $block)
    {
        $this->getBlockCloser()->closeUnmatchedBlocks();
        $block->setStartLine($this->lineNumber);
        while (!$this->tip->canContain($block)) {
            $this->tip->finalize($this);
        }

        $this->tip->addChild($block);
        $this->tip = $block;
        $this->container = $block;

        return $block;
    }

    /**
     * @param AbstractBlock $replacement
     */
    public function replaceContainerBlock(AbstractBlock $replacement)
    {
        $this->getBlockCloser()->closeUnmatchedBlocks();
        $this->getContainer()->getParent()->replaceChild($this, $this->getContainer(), $replacement);
        $this->setContainer($replacement);
    }

    /**
     * @return bool
     */
    public function getBlocksParsed()
    {
        return $this->blocksParsed;
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setBlocksParsed($bool)
    {
        $this->blocksParsed = $bool;

        return $this;
    }

    /**
     * @return ReferenceParser
     */
    public function getReferenceParser()
    {
        return $this->referenceParser;
    }
}
