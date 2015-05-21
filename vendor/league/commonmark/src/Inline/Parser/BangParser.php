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
use League\CommonMark\Delimiter\Delimiter;
use League\CommonMark\InlineParserContext;
use League\CommonMark\Inline\Element\Text;

class BangParser extends AbstractInlineParser
{
    /**
     * @return string[]
     */
    public function getCharacters()
    {
        return array('!');
    }

    /**
     * @param ContextInterface $context
     * @param InlineParserContext $inlineContext
     *
     * @return bool
     */
    public function parse(ContextInterface $context, InlineParserContext $inlineContext)
    {
        $cursor = $inlineContext->getCursor();
        if ($cursor->peek() === '[') {
            $cursor->advanceBy(2);
            $inlineContext->getInlines()->add(new Text('![', array('delim' => true)));

            // Add entry to stack for this opener
            $delimiter = new Delimiter('!', 1, $inlineContext->getInlines()->count() - 1, true, false, $cursor->getPosition());
            $inlineContext->getDelimiterStack()->push($delimiter);

            return true;
        }

        return false;
    }
}
