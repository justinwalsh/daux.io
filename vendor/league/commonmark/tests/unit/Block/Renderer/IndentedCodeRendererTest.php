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

namespace League\CommonMark\Tests\Unit\Block\Renderer;

use League\CommonMark\Block\Element\IndentedCode;
use League\CommonMark\Block\Renderer\IndentedCodeRenderer;
use League\CommonMark\HtmlElement;
use League\CommonMark\Tests\Unit\FakeHtmlRenderer;

class IndentedCodeRendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var IndentedCodeRenderer
     */
    protected $renderer;

    protected function setUp()
    {
        $this->renderer = new IndentedCodeRenderer();
    }

    public function testRender()
    {
        $block = new IndentedCode();
        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($block, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('pre', $result->getTagName());

        $code = $result->getContents(false);
        $this->assertTrue($code instanceof HtmlElement);
        $this->assertEquals('code', $code->getTagName());
        $this->assertNull($code->getAttribute('class'));
        $this->assertContains('::escape::', $code->getContents(true));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRenderWithInvalidType()
    {
        $inline = $this->getMockForAbstractClass('League\CommonMark\Block\Element\AbstractBlock');
        $fakeRenderer = new FakeHtmlRenderer();

        $this->renderer->render($inline, $fakeRenderer);
    }
}
