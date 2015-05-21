<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark;

class CursorState
{
    /**
     * @var string
     */
    private $line;

    /**
     * @var int
     */
    private $length;

    /**
     * @var int
     */
    private $currentPosition;

    /**
     * @var int
     */
    private $previousPosition;

    /**
     * @var int|null
     */
    private $firstNonSpaceCache;

    /**
     * @param string $line
     * @param int $length
     * @param int $currentPosition
     * @param int $previousPosition
     * @param int|null $firstNonSpaceCache
     */
    public function __construct($line, $length, $currentPosition, $previousPosition, $firstNonSpaceCache)
    {
        $this->line = $line;
        $this->length = $length;
        $this->currentPosition = $currentPosition;
        $this->previousPosition = $previousPosition;
        $this->firstNonSpaceCache = $firstNonSpaceCache;
    }

    /**
     * @return int
     */
    public function getCurrentPosition()
    {
        return $this->currentPosition;
    }

    /**
     * @return int|null
     */
    public function getFirstNonSpaceCache()
    {
        return $this->firstNonSpaceCache;
    }

    /**
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @return string
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @return int
     */
    public function getPreviousPosition()
    {
        return $this->previousPosition;
    }
}
