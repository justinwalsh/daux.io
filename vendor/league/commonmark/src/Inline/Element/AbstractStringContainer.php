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

namespace League\CommonMark\Inline\Element;

class AbstractStringContainer extends AbstractInline
{
    /**
     * @var string
     */
    protected $content = '';

    /**
     * @param string $contents
     * @param array $data
     */
    public function __construct($contents = '', array $data = array())
    {
        $this->content = $contents;
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $contents
     *
     * @return $this
     */
    public function setContent($contents)
    {
        $this->content = $contents;

        return $this;
    }
}
