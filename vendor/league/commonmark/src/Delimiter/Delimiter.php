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

namespace League\CommonMark\Delimiter;

class Delimiter
{
    /** @var String */
    protected $char;

    /** @var int */
    protected $numDelims;

    /** @var int */
    protected $pos;

    /** @var Delimiter|null */
    protected $previous;

    /** @var Delimiter|null */
    protected $next;

    /** @var bool */
    protected $canOpen;

    /** @var bool */
    protected $canClose;

    /** @var bool */
    protected $active;

    /** @var int|null */
    protected $index;

    /**
     * @param string    $char
     * @param int       $numDelims
     * @param int       $pos
     * @param bool      $canOpen
     * @param bool      $canClose
     * @param int|null  $index
     */
    public function __construct($char, $numDelims, $pos, $canOpen, $canClose, $index = null)
    {
        $this->char = $char;
        $this->numDelims = $numDelims;
        $this->pos = $pos;
        $this->canOpen = $canOpen;
        $this->canClose = $canClose;
        $this->active = true;
        $this->index = $index;
    }

    /**
     * @return boolean
     */
    public function canClose()
    {
        return $this->canClose;
    }

    /**
     * @param boolean $canClose
     *
     * @return $this
     */
    public function setCanClose($canClose)
    {
        $this->canClose = $canClose;

        return $this;
    }

    /**
     * @return boolean
     */
    public function canOpen()
    {
        return $this->canOpen;
    }

    /**
     * @param boolean $canOpen
     *
     * @return $this
     */
    public function setCanOpen($canOpen)
    {
        $this->canOpen = $canOpen;

        return $this;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     *
     * @return $this
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return String
     */
    public function getChar()
    {
        return $this->char;
    }

    /**
     * @param String $char
     *
     * @return $this
     */
    public function setChar($char)
    {
        $this->char = $char;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @param int|null $index
     *
     * @return $this
     */
    public function setIndex($index)
    {
        $this->index = $index;

        return $this;
    }

    /**
     * @return Delimiter|null
     */
    public function getNext()
    {
        return $this->next;
    }

    /**
     * @param Delimiter|null $next
     *
     * @return $this
     */
    public function setNext($next)
    {
        $this->next = $next;

        return $this;
    }

    /**
     * @return int
     */
    public function getNumDelims()
    {
        return $this->numDelims;
    }

    /**
     * @param int $numDelims
     *
     * @return $this
     */
    public function setNumDelims($numDelims)
    {
        $this->numDelims = $numDelims;

        return $this;
    }

    /**
     * @return int
     */
    public function getPos()
    {
        return $this->pos;
    }

    /**
     * @param int $pos
     *
     * @return $this
     */
    public function setPos($pos)
    {
        $this->pos = $pos;

        return $this;
    }

    /**
     * @return Delimiter|null
     */
    public function getPrevious()
    {
        return $this->previous;
    }

    /**
     * @param Delimiter|null $previous
     *
     * @return $this
     */
    public function setPrevious($previous)
    {
        $this->previous = $previous;

        return $this;
    }
}
