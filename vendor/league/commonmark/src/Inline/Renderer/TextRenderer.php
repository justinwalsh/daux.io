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

namespace League\CommonMark\Inline\Renderer;

use League\CommonMark\HtmlRendererInterface;
use League\CommonMark\Inline\Element\AbstractInline;
use League\CommonMark\Inline\Element\Text;

class TextRenderer implements InlineRendererInterface
{
    /**
     * @param Text $inline
     * @param HtmlRendererInterface $htmlRenderer
     *
     * @return string
     */
    public function render(AbstractInline $inline, HtmlRendererInterface $htmlRenderer)
    {
        if (!($inline instanceof Text)) {
            throw new \InvalidArgumentException('Incompatible inline type: ' . get_class($inline));
        }

        return $htmlRenderer->escape($inline->getContent());
    }
}
