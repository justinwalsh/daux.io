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
use League\CommonMark\Util\RegexHelper;

class EmphasisParser extends AbstractInlineParser
{
    /**
     * @return string[]
     */
    public function getCharacters()
    {
        return array('*', '_');
    }

    /**
     * @param ContextInterface $context
     * @param InlineParserContext $inlineContext
     *
     * @return bool
     */
    public function parse(ContextInterface $context, InlineParserContext $inlineContext)
    {
        $character = $inlineContext->getCursor()->getCharacter();
        if (!in_array($character, $this->getCharacters())) {
            return false;
        }

        $numDelims = 0;

        $cursor = $inlineContext->getCursor();
        $charBefore = $cursor->peek(-1);
        if ($charBefore === null) {
            $charBefore = "\n";
        }

        while ($cursor->peek($numDelims) === $character) {
            ++$numDelims;
        }

        $cursor->advanceBy($numDelims);

        $charAfter = $cursor->getCharacter();
        if ($charAfter === null) {
            $charAfter = "\n";
        }

        $leftFlanking = $numDelims > 0 && !preg_match('/\pZ|\s/u', $charAfter) &&
            !(preg_match(RegexHelper::REGEX_PUNCTUATION, $charAfter) &&
            !preg_match('/\pZ|\s/u', $charBefore) &&
            !(preg_match(RegexHelper::REGEX_PUNCTUATION, $charBefore)));

        $rightFlanking = $numDelims > 0 && !preg_match('/\pZ|\s/u', $charBefore) &&
            !(preg_match(RegexHelper::REGEX_PUNCTUATION, $charBefore) &&
            !preg_match('/\pZ|\s/u', $charAfter) &&
            !(preg_match(RegexHelper::REGEX_PUNCTUATION, $charAfter)));

        if ($character === '_') {
            $canOpen = $leftFlanking && (!$rightFlanking || preg_match(RegexHelper::REGEX_PUNCTUATION, $charBefore));
            $canClose = $rightFlanking && (!$leftFlanking || preg_match(RegexHelper::REGEX_PUNCTUATION, $charAfter));
        } else {
            $canOpen = $leftFlanking;
            $canClose = $rightFlanking;
        }

        $inlineContext->getInlines()->add(
            new Text($cursor->getPreviousText(), array('delim' => true))
        );

        // Add entry to stack to this opener
        $delimiter = new Delimiter($character, $numDelims, $inlineContext->getInlines()->count() - 1, $canOpen, $canClose);
        $inlineContext->getDelimiterStack()->push($delimiter);

        return true;
    }
}
