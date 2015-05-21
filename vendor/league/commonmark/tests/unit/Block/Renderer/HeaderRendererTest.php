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

use League\CommonMark\Block\Element\Header;
use League\CommonMark\Block\Renderer\HeaderRenderer;
use League\CommonMark\HtmlElement;
use League\CommonMark\Tests\Unit\FakeHtmlRenderer;

class HeaderRendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var HeaderRenderer
     */
    protected $renderer;

    protected function setUp()
    {
        $this->renderer = new HeaderRenderer();
    }

    /**
     * @param int    $level
     * @param string $expectedTag
     *
     * @dataProvider dataForTestRender
     */
    public function testRender($level, $expectedTag)
    {
        $block = new Header($level, 'test');
        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($block, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals($expectedTag, $result->getTagName());
        $this->assertContains('::inlines::', $result->getContents(true));
    }

    public function dataForTestRender()
    {
        return [
            [1, 'h1'],
            [2, 'h2'],
            [3, 'h3'],
            [4, 'h4'],
            [5, 'h5'],
            [6, 'h6'],
        ];
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
