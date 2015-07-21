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

use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;

class ListBlock extends AbstractBlock
{
    const TYPE_UNORDERED = 'Bullet';
    const TYPE_ORDERED = 'Ordered';

    /**
     * @var bool
     */
    protected $tight = false;

    /**
     * @var ListData
     */
    protected $listData;

    public function __construct(ListData $listData)
    {
        parent::__construct();

        $this->listData = $listData;
    }

    /**
     * @return ListData
     */
    public function getListData()
    {
        return $this->listData;
    }

    /**
     * @return bool
     */
    public function endsWithBlankLine()
    {
        if ($this->lastLineBlank) {
            return true;
        }

        if ($this->hasChildren()) {
            return $this->getLastChild()->endsWithBlankLine();
        }

        return false;
    }

    /**
     * Returns true if this block can contain the given block as a child node
     *
     * @param AbstractBlock $block
     *
     * @return bool
     */
    public function canContain(AbstractBlock $block)
    {
        return $block instanceof ListItem;
    }

    /**
     * Returns true if block type can accept lines of text
     *
     * @return bool
     */
    public function acceptsLines()
    {
        return false;
    }

    /**
     * Whether this is a code block
     *
     * @return bool
     */
    public function isCode()
    {
        return false;
    }

    public function matchesNextLine(Cursor $cursor)
    {
        return true;
    }

    public function finalize(ContextInterface $context)
    {
        parent::finalize($context);

        $this->tight = true; // tight by default

        foreach ($this->children as $item) {
            // check for non-final list item ending with blank line:
            if ($item->endsWithBlankLine() && $item !== $this->getLastChild()) {
                $this->tight = false;
                break;
            }

            // Recurse into children of list item, to see if there are
            // spaces between any of them:
            foreach ($item->getChildren() as $subItem) {
                if ($subItem->endsWithBlankLine() && ($item !== $this->getLastChild() || $subItem !== $item->getLastChild())) {
                    $this->tight = false;
                    break;
                }
            }
        }
    }

    /**
     * @return bool
     */
    public function isTight()
    {
        return $this->tight;
    }

    /**
     * @param ContextInterface $context
     * @param Cursor           $cursor
     */
    public function handleRemainingContents(ContextInterface $context, Cursor $cursor)
    {
        // create paragraph container for line
        $context->addBlock(new Paragraph());
        $cursor->advanceToFirstNonSpace();
        $context->getTip()->addLine($cursor->getRemainder());
    }
}
