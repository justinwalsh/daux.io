<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Block\Parser;

abstract class AbstractBlockParser implements BlockParserInterface
{
    /**
     * @return string
     */
    public function getName()
    {
        $reflection = new \ReflectionClass($this);

        return $reflection->getShortName();
    }
}
