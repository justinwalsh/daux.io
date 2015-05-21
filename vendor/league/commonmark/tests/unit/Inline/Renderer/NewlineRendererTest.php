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

use League\CommonMark\Inline\Element\Newline;
use League\CommonMark\Inline\Renderer\NewlineRenderer;
use League\CommonMark\Tests\Unit\FakeHtmlRenderer;

class NewlineRendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NewlineRenderer
     */
    protected $renderer;

    protected function setUp()
    {
        $this->renderer = new NewlineRenderer();
    }

    public function testRenderHardbreak()
    {
        $inline = new Newline(Newline::HARDBREAK);
        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($inline, $fakeRenderer);

        $this->assertInternalType('string', $result);
        $this->assertContains('<br />', $result);
    }

    public function testRenderSoftbreak()
    {
        $inline = new Newline(Newline::SOFTBREAK);
        $fakeRenderer = new FakeHtmlRenderer();
        $fakeRenderer->setOption('soft_break', '::softbreakChar::');

        $result = $this->renderer->render($inline, $fakeRenderer);

        $this->assertInternalType('string', $result);
        $this->assertContains('::softbreakChar::', $result);
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
