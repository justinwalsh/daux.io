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
use League\CommonMark\Inline\Element\AbstractInline;

/**
 * Renders a parsed AST to HTML
 */
class HtmlRenderer implements HtmlRendererInterface
{
    /**
     * @var Environment
     */
    protected $environment;

    /**
     * @param Environment $environment
     */
    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @param string $option
     * @param mixed|null $default
     *
     * @return mixed|null
     */
    public function getOption($option, $default = null)
    {
        return $this->environment->getConfig('renderer/' . $option, $default);
    }

    /**
     * @param string $string
     * @param bool   $preserveEntities
     *
     * @return string
     */
    public function escape($string, $preserveEntities = false)
    {
        if ($preserveEntities) {
            $string = preg_replace('/[&](?![#](x[a-f0-9]{1,8}|[0-9]{1,8});|[a-z][a-z0-9]{1,31};)/i', '&amp;', $string);
        } else {
            $string = str_replace('&', '&amp;', $string);
        }

        return str_replace(array('<', '>', '"'), array('&lt;', '&gt;', '&quot;'), $string);
    }

    /**
     * @param AbstractInline $inline
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected function renderInline(AbstractInline $inline)
    {
        $renderer = $this->environment->getInlineRendererForClass(get_class($inline));
        if (!$renderer) {
            throw new \RuntimeException('Unable to find corresponding renderer for block type ' . get_class($inline));
        }

        return $renderer->render($inline, $this);
    }

    /**
     * @param AbstractInline[] $inlines
     *
     * @return string
     */
    public function renderInlines($inlines)
    {
        $result = array();
        foreach ($inlines as $inline) {
            $result[] = $this->renderInline($inline);
        }

        return implode('', $result);
    }

    /**
     * @param AbstractBlock $block
     * @param bool         $inTightList
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    public function renderBlock(AbstractBlock $block, $inTightList = false)
    {
        $renderer = $this->environment->getBlockRendererForClass(get_class($block));
        if (!$renderer) {
            throw new \RuntimeException('Unable to find corresponding renderer for block type ' . get_class($block));
        }

        return $renderer->render($block, $this, $inTightList);
    }

    /**
     * @param AbstractBlock[] $blocks
     * @param bool            $inTightList
     *
     * @return string
     */
    public function renderBlocks($blocks, $inTightList = false)
    {
        $result = array();
        foreach ($blocks as $block) {
            $result[] = $this->renderBlock($block, $inTightList);
        }

        $separator = $this->getOption('block_separator', "\n");

        return implode($separator, $result);
    }
}
