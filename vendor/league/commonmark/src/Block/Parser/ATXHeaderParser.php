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
use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;
use League\CommonMark\Util\RegexHelper;

class ATXHeaderParser extends AbstractBlockParser
{
    /**
     * @param ContextInterface $context
     * @param Cursor $cursor
     *
     * @return bool
     */
    public function parse(ContextInterface $context, Cursor $cursor)
    {
        $match = RegexHelper::matchAll('/^#{1,6}(?: +|$)/', $cursor->getLine(), $cursor->getFirstNonSpacePosition());
        if (!$match) {
            return false;
        }

        $cursor->advanceToFirstNonSpace();

        $cursor->advanceBy(strlen($match[0]));

        $level = strlen(trim($match[0]));
        $str = $cursor->getRemainder();
        $str = preg_replace('/^ *#+ *$/', '', $str);
        $str = preg_replace('/ +#+ *$/', '', $str);

        $context->addBlock(new Header($level, $str));
        $context->setBlocksParsed(true);

        return true;
    }
}
