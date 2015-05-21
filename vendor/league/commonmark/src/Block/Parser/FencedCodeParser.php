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

use League\CommonMark\Block\Element\FencedCode;
use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;

class FencedCodeParser extends AbstractBlockParser
{
    /**
     * @param ContextInterface $context
     * @param Cursor $cursor
     *
     * @return bool
     */
    public function parse(ContextInterface $context, Cursor $cursor)
    {
        $previousState = $cursor->saveState();
        $indent = $cursor->advanceToFirstNonSpace();
        $fence = $cursor->match('/^`{3,}(?!.*`)|^~{3,}(?!.*~)/');
        if (is_null($fence)) {
            $cursor->restoreState($previousState);

            return false;
        }

        // fenced code block
        $fenceLength = strlen($fence);
        $context->addBlock(new FencedCode($fenceLength, $fence[0], $indent));

        return true;
    }
}
