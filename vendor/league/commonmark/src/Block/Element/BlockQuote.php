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

class BlockQuote extends AbstractBlock
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
        return true;
    }

    /**
     * Returns true if block type can accept lines of text
     *
     * @return bool
     */
    public function acceptsLines()
    {
        return false;
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
        if ($cursor->getIndent() <= 3 && $cursor->getFirstNonSpaceCharacter() === '>') {
            $cursor->advanceToFirstNonSpace();
            $cursor->advance();
            if ($cursor->getCharacter() === ' ') {
                $cursor->advance();
            }

            return true;
        }

        return false;
    }

    /**
     * @param ContextInterface $context
     * @param Cursor           $cursor
     */
    public function handleRemainingContents(ContextInterface $context, Cursor $cursor)
    {
        if ($cursor->isBlank()) {
            return;
        }

        $context->addBlock(new Paragraph());
        $cursor->advanceToFirstNonSpace();
        $context->getTip()->addLine($cursor->getRemainder());
    }

    /**
     * @param Cursor $cursor
     * @param int $currentLineNumber
     *
     * @return $this
     */
    public function setLastLineBlank(Cursor $cursor, $currentLineNumber)
    {
        parent::setLastLineBlank($cursor, $currentLineNumber);

        $this->lastLineBlank = false;
    }
}
