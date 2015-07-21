<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Inline\Parser;

/**
 * Class AbstractInlineParser
 */
abstract class AbstractInlineParser implements InlineParserInterface
{
    /**
     * @return string
     *   Name of the parser (must be unique within its block type)
     */
    public function getName()
    {
        $reflection = new \ReflectionClass($this);

        return $reflection->getShortName();
    }
}
