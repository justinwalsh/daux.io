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

namespace League\CommonMark\Block\Parser;

use League\CommonMark\Block\Element\ListBlock;
use League\CommonMark\Block\Element\ListData;
use League\CommonMark\Block\Element\ListItem;
use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;
use League\CommonMark\Util\RegexHelper;

class ListParser extends AbstractBlockParser
{
    /**
     * @param ContextInterface $context
     * @param Cursor $cursor
     *
     * @return bool
     */
    public function parse(ContextInterface $context, Cursor $cursor)
    {
        $tmpCursor = clone $cursor;
        $indent = $tmpCursor->advanceWhileMatches(' ', 3);

        $rest = $tmpCursor->getRemainder();

        $data = new ListData();

        if ($matches = RegexHelper::matchAll('/^[*+-]( +|$)/', $rest)) {
            $spacesAfterMarker = strlen($matches[1]);
            $data->type = ListBlock::TYPE_UNORDERED;
            $data->delimiter = null;
            $data->bulletChar = $matches[0][0];
        } elseif ($matches = RegexHelper::matchAll('/^(\d+)([.)])( +|$)/', $rest)) {
            $spacesAfterMarker = strlen($matches[3]);
            $data->type = ListBlock::TYPE_ORDERED;
            $data->start = intval($matches[1]);
            $data->delimiter = $matches[2];
            $data->bulletChar = null;
        } else {
            return false;
        }

        $data->padding = $this->calculateListMarkerPadding($matches[0], $spacesAfterMarker, $rest);

        $cursor->advanceToFirstNonSpace();
        $cursor->advanceBy($data->padding);

        // list item
        $data->markerOffset = $indent;

        // add the list if needed
        $container = $context->getContainer();
        if (!$container || !($context->getContainer() instanceof ListBlock) || !$data->equals($container->getListData())) {
            $context->addBlock(new ListBlock($data));
        }

        // add the list item
        $context->addBlock(new ListItem($data));

        return true;
    }

    /**
     * @param string $marker
     * @param int $spacesAfterMarker
     * @param string $rest
     *
     * @return int
     */
    private function calculateListMarkerPadding($marker, $spacesAfterMarker, $rest)
    {
        $markerLength = strlen($marker);

        if ($spacesAfterMarker >= 5 || $spacesAfterMarker < 1 || $markerLength === strlen($rest)) {
            return $markerLength - $spacesAfterMarker + 1;
        }

        return $markerLength;
    }
}
