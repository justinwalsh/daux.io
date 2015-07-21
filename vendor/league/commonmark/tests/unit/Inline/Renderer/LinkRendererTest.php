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

namespace League\CommonMark\Tests\Unit\Inline\Renderer;

use League\CommonMark\HtmlElement;
use League\CommonMark\Inline\Element\Link;
use League\CommonMark\Inline\Renderer\LinkRenderer;
use League\CommonMark\Tests\Unit\FakeHtmlRenderer;

class LinkRendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LinkRenderer
     */
    protected $renderer;

    protected function setUp()
    {
        $this->renderer = new LinkRenderer();
    }

    public function testRenderWithTitle()
    {
        $inline = new Link('http://example.com/foo.html', '::label::', '::title::');
        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($inline, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('a', $result->getTagName());
        $this->assertContains('http://example.com/foo.html', $result->getAttribute('href'));
        $this->assertContains('::escape::', $result->getAttribute('href'));
        $this->assertContains('::title::', $result->getAttribute('title'));
        $this->assertContains('::inlines::', $result->getContents(true));
    }

    public function testRenderWithoutTitle()
    {
        $inline = new Link('http://example.com/foo.html', '::label::');
        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($inline, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('a', $result->getTagName());
        $this->assertContains('http://example.com/foo.html', $result->getAttribute('href'));
        $this->assertContains('::escape::', $result->getAttribute('href'));
        $this->assertNull($result->getAttribute('title'));
        $this->assertContains('::inlines::', $result->getContents(true));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRenderWithInvalidType()
    {
        $inline = $this->getMockForAbstractClass('League\CommonMark\Inline\Element\AbstractInline');
        $fakeRenderer = new FakeHtmlRenderer();

        $this->renderer->render($inline, $fakeRenderer);
    }
}
