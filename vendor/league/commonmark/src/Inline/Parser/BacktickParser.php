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
use League\CommonMark\Inline\Element\Code;
use League\CommonMark\Inline\Element\Text;

class BacktickParser extends AbstractInlineParser
{
    /**
     * @return string[]
     */
    public function getCharacters()
    {
        return array('`');
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

        $ticks = $cursor->match('/^`+/');
        if ($ticks === '') {
            return false;
        }

        $previousState = $cursor->saveState();

        while ($matchingTicks = $cursor->match('/`+/m')) {
            if ($matchingTicks === $ticks) {
                $code = mb_substr($cursor->getLine(), $previousState->getCurrentPosition(), $cursor->getPosition() - $previousState->getCurrentPosition() - strlen($ticks), 'utf-8');
                $c = preg_replace('/[ \n]+/', ' ', $code);
                $inlineContext->getInlines()->add(new Code(trim($c)));

                return true;
            }
        }

        // If we got here, we didn't match a closing backtick sequence
        $cursor->restoreState($previousState);
        $inlineContext->getInlines()->add(new Text($ticks));

        return true;
    }
}
