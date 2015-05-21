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

namespace League\CommonMark\Util;

class TextHelper
{
    /**
     * @param string $string
     *
     * @return string
     */
    public static function detabLine($string)
    {
        if (strpos($string, "\t") === false) {
            return $string;
        }

        // Split into different parts
        $parts = explode("\t", $string);
        // Add each part to the resulting line
        // The first one is done here; others are prefixed
        // with the necessary spaces inside the loop below
        $line = array_shift($parts);

        foreach ($parts as $part) {
            // Calculate number of spaces; insert them followed by the non-tab contents
            $amount = 4 - mb_strlen($line, 'UTF-8') % 4;
            $line .= str_repeat(' ', $amount) . $part;
        }

        return $line;
    }
}
