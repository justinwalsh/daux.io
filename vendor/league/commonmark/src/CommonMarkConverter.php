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

/**
 * Converts CommonMark-compatible Markdown to HTML.
 */
class CommonMarkConverter
{
    /**
     * The document parser instance.
     *
     * @var \League\CommonMark\DocParser
     */
    protected $docParser;

    /**
     * The html renderer instance.
     *
     * @var \League\CommonMark\HtmlRendererInterface
     */
    protected $htmlRenderer;

    /**
     * Create a new commonmark converter instance.
     *
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        $environment = Environment::createCommonMarkEnvironment();
        $environment->mergeConfig($config);
        $this->docParser = new DocParser($environment);
        $this->htmlRenderer = new HtmlRenderer($environment);
    }

    /**
     * Converts CommonMark to HTML.
     *
     * @param string $commonMark
     *
     * @return string
     *
     * @api
     */
    public function convertToHtml($commonMark)
    {
        $documentAST = $this->docParser->parse($commonMark);

        return $this->htmlRenderer->renderBlock($documentAST);
    }
}
