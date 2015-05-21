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
use League\CommonMark\InlineParserContext;
use League\CommonMark\Inline\Element\Newline;
use League\CommonMark\Inline\Element\Text;
use League\CommonMark\Util\RegexHelper;

class EscapableParser extends AbstractInlineParser
{
    /**
     * @return string[]
     */
    public function getCharacters()
    {
        return array('\\');
    }

    /**
     * @param ContextInterface $context
     * @param InlineParserContext $inlineContext
     * @return bool
     */
    public function parse(ContextInterface $context, InlineParserContext $inlineContext)
    {
        $cursor = $inlineContext->getCursor();
        if ($cursor->getCharacter() !== '\\') {
            return false;
        }

        $nextChar = $cursor->peek();

        if ($nextChar === "\n") {
            $cursor->advanceBy(2);
            $inlineContext->getInlines()->add(new Newline(Newline::HARDBREAK));

            return true;
        } elseif ($nextChar !== null &&
            preg_match('/' . RegexHelper::REGEX_ESCAPABLE . '/', $nextChar)
        ) {
            $cursor->advanceBy(2);
            $inlineContext->getInlines()->add(new Text($nextChar));

            return true;
        }


        $cursor->advance();
        $inlineContext->getInlines()->add(new Text('\\'));

        return true;
    }
}
