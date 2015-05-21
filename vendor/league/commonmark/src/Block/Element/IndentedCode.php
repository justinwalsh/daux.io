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

use League\CommonMark\Block\Parser\IndentedCodeParser;
use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;

class IndentedCode extends AbstractBlock
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
        return true;
    }

    public function matchesNextLine(Cursor $cursor)
    {
        if ($cursor->getIndent() >= IndentedCodeParser::CODE_INDENT_LEVEL) {
            $cursor->advanceBy(IndentedCodeParser::CODE_INDENT_LEVEL);
        } elseif ($cursor->isBlank()) {
            $cursor->advanceToFirstNonSpace();
        } else {
            return false;
        }

        return true;
    }

    public function finalize(ContextInterface $context)
    {
        parent::finalize($context);

        $reversed = array_reverse($this->getStrings(), true);
        foreach ($reversed as $index => $line) {
            if ($line == '' || $line === "\n" || preg_match('/^(\n *)$/', $line)) {
                unset($reversed[$index]);
            } else {
                break;
            }
        }
        $fixed = array_reverse($reversed);
        $tmp = implode("\n", $fixed);
        if (substr($tmp, -1) !== "\n") {
            $tmp .= "\n";
        }

        $this->finalStringContents = $tmp;
    }

    /**
     * @param ContextInterface $context
     * @param Cursor           $cursor
     */
    public function handleRemainingContents(ContextInterface $context, Cursor $cursor)
    {
        $context->getTip()->addLine($cursor->getRemainder());
    }
}
