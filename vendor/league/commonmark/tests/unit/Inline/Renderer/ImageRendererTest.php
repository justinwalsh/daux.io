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
use League\CommonMark\Inline\Element\Image;
use League\CommonMark\Inline\Renderer\ImageRenderer;
use League\CommonMark\Tests\Unit\FakeHtmlRenderer;

class ImageRendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ImageRenderer
     */
    protected $renderer;

    protected function setUp()
    {
        $this->renderer = new ImageRenderer();
    }

    public function testRenderWithTitle()
    {
        $inline = new Image('http://example.com/foo.jpg', '::label::', '::title::');
        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($inline, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('img', $result->getTagName());
        $this->assertContains('http://example.com/foo.jpg', $result->getAttribute('src'));
        $this->assertContains('::escape::', $result->getAttribute('src'));
        $this->assertContains('::inlines::', $result->getAttribute('alt'));
        $this->assertContains('::title::', $result->getAttribute('title'));
    }

    public function testRenderWithoutTitle()
    {
        $inline = new Image('http://example.com/foo.jpg', '::label::');
        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($inline, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('img', $result->getTagName());
        $this->assertContains('http://example.com/foo.jpg', $result->getAttribute('src'));
        $this->assertContains('::escape::', $result->getAttribute('src'));
        $this->assertContains('::inlines::', $result->getAttribute('alt'));
        $this->assertNull($result->getAttribute('title'));
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
