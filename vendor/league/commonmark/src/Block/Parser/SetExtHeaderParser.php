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

use League\CommonMark\Block\Element\Header;
use League\CommonMark\Block\Element\Paragraph;
use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;
use League\CommonMark\Util\RegexHelper;

class SetExtHeaderParser extends AbstractBlockParser
{
    /**
     * @param ContextInterface $context
     * @param Cursor $cursor
     *
     * @return bool
     */
    public function parse(ContextInterface $context, Cursor $cursor)
    {
        if (!($context->getContainer() instanceof Paragraph)) {
            return false;
        }

        if (count($context->getContainer()->getStrings()) !== 1) {
            return false;
        }

        $match = RegexHelper::matchAll('/^(?:=+|-+) *$/', $cursor->getLine(), $cursor->getFirstNonSpacePosition());
        if ($match === null) {
            return false;
        }

        $level = $match[0][0] === '=' ? 1 : 2;
        $strings = $context->getContainer()->getStrings();

        $context->replaceContainerBlock(new Header($level, reset($strings) ?: ''));

        return true;
    }
}
