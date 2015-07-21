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

class DelimiterStack
{
    /**
     * @var Delimiter|null
     */
    protected $top;

    public function getTop()
    {
        return $this->top;
    }

    public function push(Delimiter $newDelimiter)
    {
        $newDelimiter->setPrevious($this->top);

        if ($this->top !== null) {
            $this->top->setNext($newDelimiter);
        }

        $this->top = $newDelimiter;
    }

    /**
     * @param Delimiter|null $stackBottom
     *
     * @return Delimiter|null
     */
    public function findEarliest(Delimiter $stackBottom = null)
    {
        $delimiter = $this->top;
        while ($delimiter !== null && $delimiter->getPrevious() !== $stackBottom) {
            $delimiter = $delimiter->getPrevious();
        }

        return $delimiter;
    }

    /**
     * @param Delimiter $delimiter
     */
    public function removeDelimiter(Delimiter $delimiter)
    {
        if ($delimiter->getPrevious() !== null) {
            $delimiter->getPrevious()->setNext($delimiter->getNext());
        }

        if ($delimiter->getNext() === null) {
            // top of stack
            $this->top = $delimiter->getPrevious();
        } else {
            $delimiter->getNext()->setPrevious($delimiter->getPrevious());
        }
    }

    /**
     * @param Delimiter|null $stackBottom
     */
    public function removeAll(Delimiter $stackBottom = null)
    {
        while ($this->top && $this->top !== $stackBottom) {
            $this->removeDelimiter($this->top);
        }
    }

    /**
     * @param string $character
     */
    public function removeEarlierMatches($character)
    {
        $opener = $this->top;
        while ($opener !== null) {
            if ($opener->getChar() === $character) {
                $opener->setActive(false);
            }

            $opener = $opener->getPrevious();
        }
    }

    /**
     * @param string|string[] $characters
     *
     * @return Delimiter|null
     */
    public function searchByCharacter($characters)
    {
        if (!is_array($characters)) {
            $characters = array($characters);
        }

        $opener = $this->top;
        while ($opener !== null) {
            if (in_array($opener->getChar(), $characters)) {
                break;
            }
            $opener = $opener->getPrevious();
        }

        return $opener;
    }

    /**
     * @param string|string[] $characters
     * @param callable $callback
     * @param Delimiter $stackBottom
     */
    public function iterateByCharacters($characters, $callback, Delimiter $stackBottom = null)
    {
        if (!is_array($characters)) {
            $characters = array($characters);
        }

        // Find first closer above stackBottom
        $closer = $this->findEarliest($stackBottom);

        while ($closer !== null) {
            if ($closer->canClose() && (in_array($closer->getChar(), $characters))) {
                // Found emphasis closer. Now look back for first matching opener:
                $opener = $closer->getPrevious();
                while ($opener !== null && $opener !== $stackBottom) {
                    if ($opener->getChar() === $closer->getChar() && $opener->canOpen()) {
                        break;
                    }
                    $opener = $opener->getPrevious();
                }

                if ($opener !== null && $opener !== $stackBottom) {
                    $closer = $callback($opener, $closer, $this);
                } else {
                    $closer = $closer->getNext();
                }
            } else {
                $closer = $closer->getNext();
            }
        }
    }
}
