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

class Paragraph extends AbstractInlineContainer
{
    /**
     * Returns true if this block can contain the given block as a child node
     *
     * @param AbstractBlock $block
     *
     * @return bool
     */
    public function canContain(AbstractBlock $block)
    {
        return false;
    }

    /**
     * Returns true if block type can accept lines of text
     *
     * @return bool
     */
    public function acceptsLines()
    {
        return true;
    }

    /**
     * Whether this is a code block
     *
     * @return bool
     */
    public function isCode()
    {
        return false;
    }

    public function matchesNextLine(Cursor $cursor)
    {
        if ($cursor->isBlank()) {
            $this->lastLineBlank = true;

            return false;
        }

        return true;
    }

    public function finalize(ContextInterface $context)
    {
        parent::finalize($context);

        $this->finalStringContents = preg_replace('/^  */m', '', implode("\n", $this->getStrings()));

        // Short-circuit
        if ($this->finalStringContents === '' || $this->finalStringContents[0] !== '[') {
            return;
        }

        $cursor = new Cursor($this->finalStringContents);

        $referenceFound = $this->parseReferences($context, $cursor);

        $this->finalStringContents = $cursor->getRemainder();

        if ($referenceFound && $cursor->isAtEnd()) {
            $this->parent->removeChild($this);
        }
    }

    /**
     * @param ContextInterface $context
     * @param Cursor $cursor
     *
     * @return bool
     */
    protected function parseReferences(ContextInterface $context, Cursor $cursor)
    {
        $referenceFound = false;
        while ($cursor->getCharacter() === '[' && $context->getReferenceParser()->parse($cursor)) {
            $this->finalStringContents = $cursor->getRemainder();
            $referenceFound = true;
        }

        return $referenceFound;
    }

    /**
     * @param ContextInterface $context
     * @param Cursor           $cursor
     */
    public function handleRemainingContents(ContextInterface $context, Cursor $cursor)
    {
        $cursor->advanceToFirstNonSpace();
        $context->getTip()->addLine($cursor->getRemainder());
    }
}
