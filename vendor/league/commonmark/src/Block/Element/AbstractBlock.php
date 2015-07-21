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

namespace League\CommonMark\Block\Element;

use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;
use League\CommonMark\Util\ArrayCollection;

/**
 * Block-level element
 */
abstract class AbstractBlock
{
    /**
     * Used for storage of arbitrary data.
     *
     * @var array
     */
    public $data = array();

    /**
     * @var ArrayCollection|AbstractBlock[]
     */
    protected $children;

    /**
     * @var AbstractBlock|null
     */
    protected $parent = null;

    /**
     * @var ArrayCollection|string[]
     */
    protected $strings;

    /**
     * @var string
     */
    protected $finalStringContents = '';

    /**
     * @var bool
     */
    protected $open = true;

    /**
     * @var bool
     */
    protected $lastLineBlank = false;

    /**
     * @var int
     */
    protected $startLine;

    /**
     * @var int
     */
    protected $endLine;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->strings = new ArrayCollection();
    }

    /**
     * @return AbstractBlock|null
     */
    public function getParent()
    {
        return $this->parent ? : null;
    }

    /**
     * @param AbstractBlock $parent
     *
     * @return $this
     */
    protected function setParent(AbstractBlock $parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasChildren()
    {
        return !$this->children->isEmpty();
    }

    /**
     * @return AbstractBlock[]
     */
    public function getChildren()
    {
        return $this->children->toArray();
    }

    /**
     * @return AbstractBlock|null
     */
    public function getLastChild()
    {
        return $this->children->last();
    }


    public function addChild(AbstractBlock $childBlock)
    {
        $this->children->add($childBlock);
        $childBlock->setParent($this);
    }

    public function removeChild(AbstractBlock $childBlock)
    {
        if (($index = $this->children->indexOf($childBlock)) !== false) {
            $this->children->remove($index);

            return true;
        }

        return false;
    }

    public function replaceChild(ContextInterface $context, AbstractBlock $original, AbstractBlock $replacement)
    {
        if (($index = $this->children->indexOf($original)) !== false) {
            $this->children->remove($index);
            $replacement->setParent($this);
            $this->children->set($index, $replacement);
        } else {
            $this->addChild($replacement);
        }

        if ($context->getTip() === $original) {
            $context->setTip($replacement);
        }
    }

    /**
     * Returns true if this block can contain the given block as a child node
     *
     * @param AbstractBlock $block
     *
     * @return bool
     */
    abstract public function canContain(AbstractBlock $block);

    /**
     * Returns true if block type can accept lines of text
     *
     * @return bool
     */
    abstract public function acceptsLines();

    /**
     * Whether this is a code block
     *
     * @return bool
     */
    abstract public function isCode();

    /**
     * @param Cursor $cursor
     *
     * @return bool
     */
    abstract public function matchesNextLine(Cursor $cursor);

    /**
     * @param ContextInterface $context
     * @param Cursor           $cursor
     */
    abstract public function handleRemainingContents(ContextInterface $context, Cursor $cursor);

    /**
     * @param int $startLine
     *
     * @return $this
     */
    public function setStartLine($startLine)
    {
        $this->startLine = $startLine;
        if (empty($this->endLine)) {
            $this->endLine = $startLine;
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getStartLine()
    {
        return $this->startLine;
    }

    public function setEndLine($endLine)
    {
        $this->endLine = $endLine;

        return $this;
    }

    /**
     * @return int
     */
    public function getEndLine()
    {
        return $this->endLine;
    }

    /**
     * Whether the block ends with a blank line
     *
     * @return bool
     */
    public function endsWithBlankLine()
    {
        return $this->lastLineBlank;
    }

    /**
     * @return string[]
     */
    public function getStrings()
    {
        return $this->strings->toArray();
    }

    /**
     * @param string $line
     */
    public function addLine($line)
    {
        if (!$this->acceptsLines()) {
            throw new \LogicException('You cannot add lines to a block which cannot accept them');
        }

        $this->strings->add($line);
    }

    /**
     * Whether the block is open for modifications
     *
     * @return bool
     */
    public function isOpen()
    {
        return $this->open;
    }

    /**
     * Finalize the block; mark it closed for modification
     *
     * @param ContextInterface $context
     */
    public function finalize(ContextInterface $context)
    {
        if (!$this->open) {
            return; // TODO: Throw AlreadyClosedException?
        }

        $this->open = false;
        if ($context->getLineNumber() > $this->getStartLine()) {
            $this->endLine = $context->getLineNumber() - 1;
        } else {
            $this->endLine = $context->getLineNumber();
        }

        $context->setTip($context->getTip()->getParent());
    }

    /**
     * @return string
     */
    public function getStringContent()
    {
        return $this->finalStringContents;
    }

    /**
     * @param Cursor $cursor
     * @param int $currentLineNumber
     *
     * @return $this
     */
    public function setLastLineBlank(Cursor $cursor, $currentLineNumber)
    {
        $this->lastLineBlank = $cursor->isBlank();

        $container = $this;
        while ($container->getParent()) {
            $container = $container->getParent();
            $container->lastLineBlank = false;
        }

        return $this;
    }
}
