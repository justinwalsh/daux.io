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

use League\CommonMark\Inline\Element\Html;
use League\CommonMark\Inline\Renderer\RawHtmlRenderer;
use League\CommonMark\Tests\Unit\FakeHtmlRenderer;

class RawHtmlRendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RawHtmlRenderer
     */
    protected $renderer;

    protected function setUp()
    {
        $this->renderer = new RawHtmlRenderer();
    }

    public function testRender()
    {
        $inline = new Html('<h1>Test</h1>');
        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($inline, $fakeRenderer);

        $this->assertInternalType('string', $result);
        $this->assertContains('<h1>Test</h1>', $result);
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
