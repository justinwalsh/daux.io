<?php
/**
 * Created by IntelliJ IDEA.
 * User: onigoetz
 * Date: 09/04/16
 * Time: 23:03
 */

namespace Todaymade\Daux\ContentTypes\Markdown\TOC;


use League\CommonMark\Block\Parser\AbstractBlockParser;
use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;

class Parser extends AbstractBlockParser
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

        $context->addBlock(new Element());

        return true;
    }
}
