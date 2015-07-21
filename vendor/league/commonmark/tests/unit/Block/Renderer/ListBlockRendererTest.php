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

use League\CommonMark\Block\Element\ListBlock;
use League\CommonMark\Block\Element\ListData;
use League\CommonMark\Block\Renderer\ListBlockRenderer;
use League\CommonMark\HtmlElement;
use League\CommonMark\Tests\Unit\FakeHtmlRenderer;

class ListBlockRendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ListBlockRenderer
     */
    protected $renderer;

    protected function setUp()
    {
        $this->renderer = new ListBlockRenderer();
    }

    /**
     * @param int|null $listStart
     * @param mixed    $expectedAttributeValue
     *
     * @dataProvider dataForTestOrderedListStartingNumber
     */
    public function testRenderOrderedList($listStart = null, $expectedAttributeValue = null)
    {
        $list = $this->createOrderedListBlock($listStart);
        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($list, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('ol', $result->getTagName());
        $this->assertSame($expectedAttributeValue, $result->getAttribute('start'));
        $this->assertContains('::blocks::', $result->getContents(true));
    }

    public function dataForTestOrderedListStartingNumber()
    {
        return array(
            array(null, null),
            array(0, '0'),
            array(1, null),
            array(2, '2'),
            array(42, '42'),
        );
    }

    public function testRenderUnorderedList()
    {
        $list = $this->createUnorderedListBlock();
        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($list, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('ul', $result->getTagName());
        $this->assertContains('::blocks::', $result->getContents(true));
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

    /**
     * @param int $start
     *
     * @return ListBlock
     */
    private function createOrderedListBlock($start)
    {
        $data = new ListData();
        $data->type = ListBlock::TYPE_ORDERED;
        $data->start = $start;

        return new ListBlock($data);
    }

    /**
     * @return ListBlock
     */
    protected function createUnorderedListBlock()
    {
        $data = new ListData();
        $data->type = ListBlock::TYPE_UNORDERED;

        return new ListBlock($data);
    }
}
