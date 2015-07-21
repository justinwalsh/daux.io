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

namespace League\CommonMark\Inline\Parser;

use League\CommonMark\ContextInterface;
use League\CommonMark\Inline\Element\Text;
use League\CommonMark\InlineParserContext;
use League\CommonMark\Inline\Element\Newline;

class NewlineParser extends AbstractInlineParser
{
    /**
     * @return string[]
     */
    public function getCharacters()
    {
        return array("\n");
    }

    /**
     * @param ContextInterface $context
     * @param InlineParserContext $inlineContext
     *
     * @return bool
     */
    public function parse(ContextInterface $context, InlineParserContext $inlineContext)
    {
        $inlineContext->getCursor()->advance();

        // Check previous inline for trailing spaces
        $spaces = 0;
        $lastInline = $inlineContext->getInlines()->last();
        if ($lastInline && $lastInline instanceof Text) {
            $trimmed = rtrim($lastInline->getContent(), ' ');
            $spaces = strlen($lastInline->getContent()) - strlen($trimmed);
            if ($spaces) {
                $lastInline->setContent($trimmed);
            }
        }

        if ($spaces >= 2) {
            $inlineContext->getInlines()->add(new Newline(Newline::HARDBREAK));
        } else {
            $inlineContext->getInlines()->add(new Newline(Newline::SOFTBREAK));
        }

        return true;
    }
}
