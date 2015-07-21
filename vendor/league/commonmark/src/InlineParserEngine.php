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

use League\CommonMark\Inline\Element\Text;
use League\CommonMark\Util\ArrayCollection;

class InlineParserEngine
{
    protected $environment;

    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @param ContextInterface $context
     * @param Cursor $cursor
     *
     * @return ArrayCollection
     */
    public function parse(ContextInterface $context, Cursor $cursor)
    {
        $inlineParserContext = new InlineParserContext($cursor);
        while (($character = $cursor->getCharacter()) !== null) {
            if (!$this->parseCharacter($character, $context, $inlineParserContext)) {
                $this->addPlainText($character, $inlineParserContext);
            }
        }

        $this->processInlines($inlineParserContext);

        return $inlineParserContext->getInlines();
    }

    /**
     * @param string              $character
     * @param ContextInterface    $context
     * @param InlineParserContext $inlineParserContext
     *
     * @return bool Whether we successfully parsed a character at that position
     */
    protected function parseCharacter($character, ContextInterface $context, InlineParserContext $inlineParserContext)
    {
        $matchingParsers = $this->environment->getInlineParsersForCharacter($character);
        if (empty($matchingParsers)) {
            return false;
        }

        foreach ($matchingParsers as $parser) {
            if ($parser->parse($context, $inlineParserContext)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param InlineParserContext $inlineParserContext
     */
    protected function processInlines(InlineParserContext $inlineParserContext)
    {
        foreach ($this->environment->getInlineProcessors() as $inlineProcessor) {
            $inlineProcessor->processInlines($inlineParserContext->getInlines(), $inlineParserContext->getDelimiterStack());
        }
    }

    /**
     * @param string              $character
     * @param InlineParserContext $inlineParserContext
     */
    private function addPlainText($character, InlineParserContext $inlineParserContext)
    {
        // We reach here if none of the parsers can handle the input
        // Attempt to match multiple non-special characters at once
        $text = $inlineParserContext->getCursor()->match($this->environment->getInlineParserCharacterRegex());
        // This might fail if we're currently at a special character which wasn't parsed; if so, just add that character
        if ($text === null) {
            $inlineParserContext->getCursor()->advance();
            $text = $character;
        }

        $lastInline = $inlineParserContext->getInlines()->last();
        if ($lastInline instanceof Text && !isset($lastInline->data['delim'])) {
            $lastInline->append($text);
        } else {
            $inlineParserContext->getInlines()->add(new Text($text));
        }
    }
}
