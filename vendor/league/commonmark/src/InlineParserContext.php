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

use League\CommonMark\Delimiter\DelimiterStack;
use League\CommonMark\Util\ArrayCollection;

class InlineParserContext
{
    private $cursor;
    private $inlines;
    private $delimiterStack;

    public function __construct(Cursor $cursor)
    {
        $this->cursor = $cursor;
        $this->inlines = new ArrayCollection();
        $this->delimiterStack = new DelimiterStack();
    }

    /**
     * @return Cursor
     */
    public function getCursor()
    {
        return $this->cursor;
    }

    /**
     * @return ArrayCollection
     */
    public function getInlines()
    {
        return $this->inlines;
    }

    /**
     * @return DelimiterStack
     */
    public function getDelimiterStack()
    {
        return $this->delimiterStack;
    }
}
