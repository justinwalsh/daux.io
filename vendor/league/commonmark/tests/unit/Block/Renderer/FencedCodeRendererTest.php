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

use League\CommonMark\Block\Element\FencedCode;
use League\CommonMark\Block\Renderer\FencedCodeRenderer;
use League\CommonMark\HtmlElement;
use League\CommonMark\Tests\Unit\FakeHtmlRenderer;

class FencedCodeRendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FencedCodeRenderer
     */
    protected $renderer;

    protected function setUp()
    {
        $this->renderer = new FencedCodeRenderer();
    }

    public function testRenderWithLanguageSpecified()
    {
        /** @var FencedCode|\PHPUnit_Framework_MockObject_MockObject $block */
        $block = $this->getMockBuilder('League\CommonMark\Block\Element\FencedCode')
            ->setConstructorArgs([3, '~', 0])
            ->getMock();

        $block->expects($this->any())
            ->method('getInfoWords')
            ->will($this->returnValue(['php']));
        $block->addLine('echo "hello world!";');

        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($block, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('pre', $result->getTagName());

        $code = $result->getContents(false);
        $this->assertTrue($code instanceof HtmlElement);
        $this->assertEquals('code', $code->getTagName());
        $this->assertContains('language-::escape::php', $code->getAttribute('class'));
        $this->assertContains('::escape::', $code->getContents(true));
    }

    public function testRenderWithoutLanguageSpecified()
    {
        /** @var FencedCode|\PHPUnit_Framework_MockObject_MockObject $block */
        $block = new FencedCode(3, '~', 0);
        $block->addLine('echo "hello world!";');

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
