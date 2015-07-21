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

namespace League\CommonMark\Inline\Processor;

use League\CommonMark\Delimiter\Delimiter;
use League\CommonMark\Delimiter\DelimiterStack;
use League\CommonMark\Util\ArrayCollection;

interface InlineProcessorInterface
{
    /**
     * @param ArrayCollection $inlines
     * @param DelimiterStack $delimiterStack
     * @param Delimiter $stackBottom
     *
     * @return void
     */
    public function processInlines(ArrayCollection $inlines, DelimiterStack $delimiterStack, Delimiter $stackBottom = null);
}
