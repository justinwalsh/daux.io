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

namespace League\CommonMark\Block\Element;

use League\CommonMark\Inline\Element\AbstractInline;
use League\CommonMark\Util\ArrayCollection;

abstract class AbstractInlineContainer extends AbstractBlock
{
    /**
     * @var ArrayCollection|AbstractInline[]
     */
    protected $inlines;

    public function __construct()
    {
        parent::__construct();

        $this->inlines = new ArrayCollection();
    }

    /**
     * @return ArrayCollection|AbstractInline[]
     */
    public function getInlines()
    {
        return $this->inlines;
    }

    /**
     * @param ArrayCollection|AbstractInline[] $inlines
     *
     * @return $this
     */
    public function setInlines($inlines)
    {
        if (is_array($inlines)) {
            $this->inlines = new ArrayCollection($inlines);
        } elseif (is_object($inlines) && $inlines instanceof ArrayCollection) {
            $this->inlines = $inlines;
        } else {
            throw new \InvalidArgumentException();
        }

        return $this;
    }
}
