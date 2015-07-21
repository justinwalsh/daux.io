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
use League\CommonMark\Block\Element\Document;

interface ContextInterface
{
    /**
     * @return Document
     */
    public function getDocument();

    /**
     * @return AbstractBlock
     */
    public function getTip();

    /**
     * @param AbstractBlock $block
     *
     * @return $this
     */
    public function setTip(AbstractBlock $block);

    /**
     * @return int
     */
    public function getLineNumber();

    /**
     * @return string
     */
    public function getLine();

    /**
     * Finalize and close any unmatched blocks
     *
     * @return UnmatchedBlockCloser
     */
    public function getBlockCloser();

    /**
     * @return AbstractBlock
     */
    public function getContainer();

    /**
     * @param AbstractBlock $getDocument
     *
     * @return $this
     */
    public function setContainer($getDocument);

    /**
     * @param AbstractBlock $block
     *
     * @return AbstractBlock
     */
    public function addBlock(AbstractBlock $block);

    /**
     * @param AbstractBlock $replacement
     *
     * @return void
     */
    public function replaceContainerBlock(AbstractBlock $replacement);

    /**
     * @return bool
     */
    public function getBlocksParsed();

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setBlocksParsed($bool);

    /**
     * @return ReferenceParser
     */
    public function getReferenceParser();
}
