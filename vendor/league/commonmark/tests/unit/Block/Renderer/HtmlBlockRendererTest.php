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

use League\CommonMark\Block\Element\HtmlBlock;
use League\CommonMark\Block\Renderer\HtmlBlockRenderer;
use League\CommonMark\Tests\Unit\FakeHtmlRenderer;

class HtmlBlockRendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var HtmlBlockRenderer
     */
    protected $renderer;

    protected function setUp()
    {
        $this->renderer = new HtmlBlockRenderer();
    }

    public function testRender()
    {
        /** @var HtmlBlock|\PHPUnit_Framework_MockObject_MockObject $block */
        $block = $this->getMock('League\CommonMark\Block\Element\HtmlBlock');
        $block->expects($this->any())
            ->method('getStringContent')
            ->will($this->returnValue('<button>Test</button>'));

        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($block, $fakeRenderer);

        $this->assertInternalType('string', $result);
        $this->assertContains('<button>Test</button>', $result);
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
