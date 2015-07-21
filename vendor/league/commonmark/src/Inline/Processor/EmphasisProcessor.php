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
use League\CommonMark\Inline\Element\Emphasis;
use League\CommonMark\Inline\Element\Strong;
use League\CommonMark\Inline\Element\Text;
use League\CommonMark\Util\ArrayCollection;

class EmphasisProcessor implements InlineProcessorInterface
{
    public function processInlines(ArrayCollection $inlines, DelimiterStack $delimiterStack, Delimiter $stackBottom = null)
    {
        $callback = function (Delimiter $opener, Delimiter $closer, DelimiterStack $stack) use ($inlines) {
            // Calculate actual number of delimiters used from this closer
            if ($closer->getNumDelims() < 3 || $opener->getNumDelims() < 3) {
                $useDelims = $closer->getNumDelims() <= $opener->getNumDelims()
                    ? $closer->getNumDelims()
                    : $opener->getNumDelims();
            } else {
                $useDelims = $closer->getNumDelims() % 2 === 0 ? 2 : 1;
            }
            /** @var Text $openerInline */
            $openerInline = $inlines->get($opener->getPos());
            /** @var Text $closerInline */
            $closerInline = $inlines->get($closer->getPos());
            // Remove used delimiters from stack elts and inlines
            $opener->setNumDelims($opener->getNumDelims() - $useDelims);
            $closer->setNumDelims($closer->getNumDelims() - $useDelims);
            $openerInline->setContent(substr($openerInline->getContent(), 0, -$useDelims));
            $closerInline->setContent(substr($closerInline->getContent(), 0, -$useDelims));
            // Build contents for new emph element
            $start = $opener->getPos() + 1;
            $contents = $inlines->slice($start, $closer->getPos() - $start);
            $contents = array_filter($contents);

            if ($useDelims === 1) {
                $emph = new Emphasis($contents);
            } else {
                $emph = new Strong($contents);
            }

            // Insert into list of inlines
            $inlines->set($opener->getPos() + 1, $emph);
            for ($i = $opener->getPos() + 2; $i < $closer->getPos(); $i++) {
                $inlines->set($i, null);
            }
            // Remove elts btw opener and closer in delimiters stack
            $tempStack = $closer->getPrevious();
            while ($tempStack !== null && $tempStack !== $opener) {
                $nextStack = $tempStack->getPrevious();
                $stack->removeDelimiter($tempStack);
                $tempStack = $nextStack;
            }
            // If opener has 0 delims, remove it and the inline
            if ($opener->getNumDelims() === 0) {
                $inlines->set($opener->getPos(), null);
                $stack->removeDelimiter($opener);
            }
            if ($closer->getNumDelims() === 0) {
                $inlines->set($closer->getPos(), null);
                $tempStack = $closer->getNext();
                $stack->removeDelimiter($closer);
                return $tempStack;
            }
            return $closer;
        };

        // Process the emphasis characters
        $delimiterStack->iterateByCharacters(array('_', '*'), $callback, $stackBottom);
        // Remove gaps
        $inlines->removeGaps();
        // Remove all delimiters
        $delimiterStack->removeAll($stackBottom);
    }
}
