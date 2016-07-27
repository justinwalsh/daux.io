<?php namespace Todaymade\Daux\ContentTypes\Markdown;

use League\CommonMark\Block\Parser\AbstractBlockParser;
use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;

class TableOfContentsParser extends AbstractBlockParser
{
    /**
     * @param ContextInterface $context
     * @param Cursor $cursor
     *
     * @return bool
     */
    public function parse(ContextInterface $context, Cursor $cursor)
    {
        if ($cursor->isIndented()) {
            return false;
        }

        $previousState = $cursor->saveState();
        $cursor->advanceToFirstNonSpace();
        $fence = $cursor->match('/^\[TOC\]/');
        if (is_null($fence)) {
            $cursor->restoreState($previousState);

            return false;
        }

        $context->addBlock(new TableOfContents());

        return true;
    }
}
