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

use League\CommonMark\Block\Element\AbstractBlock;

class UnmatchedBlockCloser
{
    /**
     * @var ContextInterface
     */
    private $context;

    /**
     * @var AbstractBlock
     */
    private $oldTip;

    /**
     * @var AbstractBlock
     */
    private $lastMatchedContainer;

    /**
     * @param ContextInterface $context
     */
    public function __construct(ContextInterface $context)
    {
        $this->context = $context;

        $this->resetTip();
    }

    /**
     * @param AbstractBlock $block
     */
    public function setLastMatchedContainer(AbstractBlock $block)
    {
        $this->lastMatchedContainer = $block;
    }

    public function closeUnmatchedBlocks()
    {
        while ($this->oldTip !== $this->lastMatchedContainer) {
            $this->oldTip->finalize($this->context);
            $this->oldTip = $this->oldTip->getParent();
        }
    }

    public function resetTip()
    {
        $this->oldTip = $this->context->getTip();
    }

    /**
     * @return bool
     */
    public function areAllClosed()
    {
        return $this->context->getTip() === $this->lastMatchedContainer;
    }
}
