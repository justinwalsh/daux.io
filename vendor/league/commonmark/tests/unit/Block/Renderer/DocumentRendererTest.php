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

use League\CommonMark\Block\Element\Document;
use League\CommonMark\Block\Renderer\DocumentRenderer;
use League\CommonMark\Tests\Unit\FakeEmptyHtmlRenderer;
use League\CommonMark\Tests\Unit\FakeHtmlRenderer;

class DocumentRendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DocumentRenderer
     */
    protected $renderer;

    protected function setUp()
    {
        $this->renderer = new DocumentRenderer();
    }

    public function testRenderEmptyDocument()
    {
        $block = new Document();
        $fakeRenderer = new FakeEmptyHtmlRenderer();

        $result = $this->renderer->render($block, $fakeRenderer);

        $this->assertInternalType('string', $result);
        $this->assertEmpty($result);
    }

    public function testRenderDocument()
    {
        $block = new Document();
        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($block, $fakeRenderer);

        $this->assertInternalType('string', $result);
        $this->assertContains('::blocks::', $result);
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
