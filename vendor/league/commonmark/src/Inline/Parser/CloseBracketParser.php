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
use League\CommonMark\Cursor;
use League\CommonMark\Delimiter\Delimiter;
use League\CommonMark\Environment;
use League\CommonMark\EnvironmentAwareInterface;
use League\CommonMark\Inline\Element\AbstractWebResource;
use League\CommonMark\InlineParserContext;
use League\CommonMark\Inline\Element\Image;
use League\CommonMark\Inline\Element\Link;
use League\CommonMark\Reference\Reference;
use League\CommonMark\Reference\ReferenceMap;
use League\CommonMark\Util\ArrayCollection;
use League\CommonMark\Util\LinkParserHelper;

class CloseBracketParser extends AbstractInlineParser implements EnvironmentAwareInterface
{
    /**
     * @var Environment
     */
    protected $environment;

    /**
     * @return string[]
     */
    public function getCharacters()
    {
        return array(']');
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

        $startPos = $cursor->getPosition();
        $previousState = $cursor->saveState();

        // Look through stack of delimiters for a [ or !
        $opener = $inlineContext->getDelimiterStack()->searchByCharacter(array('[', '!'));
        if ($opener === null) {
            return false;
        }

        if (!$opener->isActive()) {
            // no matched opener; remove from emphasis stack
            $inlineContext->getDelimiterStack()->removeDelimiter($opener);

            return false;
        }

        $isImage = $opener->getChar() === '!';

        // Instead of copying a slice, we null out the parts of inlines that don't correspond to linkText; later, we'll
        // collapse them. This is awkward, and could  be simplified if we made inlines a linked list instead
        $inlines = $inlineContext->getInlines();
        $labelInlines = new ArrayCollection($inlines->toArray());
        $this->nullify($labelInlines, 0, $opener->getPos() + 1);

        $cursor->advance();

        // Check to see if we have a link/image
        if (!($link = $this->tryParseLink($cursor, $context->getDocument()->getReferenceMap(), $opener, $startPos))) {
            // No match
            $inlineContext->getDelimiterStack()->removeDelimiter($opener); // Remove this opener from stack
            $cursor->restoreState($previousState);

            return false;
        }

        foreach ($this->environment->getInlineProcessors() as $inlineProcessor) {
            $inlineProcessor->processInlines($labelInlines, $inlineContext->getDelimiterStack(), $opener->getPrevious());
        }

        // Remove the part of inlines that become link_text
        $this->nullify($inlines, $opener->getPos(), $inlines->count());

        // processEmphasis will remove this and later delimiters.
        // Now, for a link, we also remove earlier link openers (no links in links)
        if (!$isImage) {
            $inlineContext->getDelimiterStack()->removeEarlierMatches('[');
        }

        $inlines->add($this->createInline($link['url'], $labelInlines, $link['title'], $isImage));

        return true;
    }

    /**
     * @param Environment $environment
     */
    public function setEnvironment(Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @param ArrayCollection $collection
     * @param int $start
     * @param int $end
     */
    protected function nullify(ArrayCollection $collection, $start, $end)
    {
        for ($i = $start; $i < $end; $i++) {
            $collection->set($i, null);
        }
    }

    /**
     * @param Cursor $cursor
     * @param ReferenceMap $referenceMap
     * @param Delimiter $opener
     * @param int $startPos
     *
     * @return array|bool
     */
    protected function tryParseLink(Cursor $cursor, ReferenceMap $referenceMap, Delimiter $opener, $startPos)
    {
        // Check to see if we have a link/image
        // Inline link?
        if ($cursor->getCharacter() == '(') {
            if ($result = $this->tryParseInlineLinkAndTitle($cursor)) {
                return $result;
            }
        } elseif ($link = $this->tryParseReference($cursor, $referenceMap, $opener, $startPos)) {
            return array('url' => $link->getDestination(), 'title' => $link->getTitle());
        }

        return false;
    }

    /**
     * @param Cursor $cursor
     *
     * @return array|bool
     */
    protected function tryParseInlineLinkAndTitle(Cursor $cursor)
    {
        $cursor->advance();
        $cursor->advanceToFirstNonSpace();
        if (($dest = LinkParserHelper::parseLinkDestination($cursor)) === null) {
            return false;
        }

        $cursor->advanceToFirstNonSpace();

        $title = null;
        // make sure there's a space before the title:
        if (preg_match('/^\\s/', $cursor->peek(-1))) {
            $title = LinkParserHelper::parseLinkTitle($cursor) ?: '';
        }

        $cursor->advanceToFirstNonSpace();

        if (!$cursor->match('/^\\)/')) {
            return false;
        }

        return array('url' => $dest, 'title' => $title);
    }

    /**
     * @param Cursor $cursor
     * @param ReferenceMap $referenceMap
     * @param Delimiter $opener
     * @param int $startPos
     *
     * @return Reference|null
     */
    protected function tryParseReference(Cursor $cursor, ReferenceMap $referenceMap, Delimiter $opener, $startPos)
    {
        $savePos = $cursor->saveState();
        $cursor->advanceToFirstNonSpace();
        $beforeLabel = $cursor->getPosition();
        $n = LinkParserHelper::parseLinkLabel($cursor);
        if ($n === 0 || $n === 2) {
            // Empty or missing second label
            $reflabel = mb_substr($cursor->getLine(), $opener->getIndex(), $startPos - $opener->getIndex(), 'utf-8');
        } else {
            $reflabel = mb_substr($cursor->getLine(), $beforeLabel + 1, $n - 2, 'utf-8');
        }

        if ($n === 0) {
            // If shortcut reference link, rewind before spaces we skipped
            $cursor->restoreState($savePos);
        }

        return $referenceMap->getReference($reflabel);
    }

    /**
     * @param string $url
     * @param ArrayCollection $labelInlines
     * @param string $title
     * @param bool $isImage
     *
     * @return AbstractWebResource
     */
    protected function createInline($url, ArrayCollection $labelInlines, $title, $isImage)
    {
        if ($isImage) {
            return new Image($url, $labelInlines, $title);
        } else {
            return new Link($url, $labelInlines, $title);
        }
    }
}
