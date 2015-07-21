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

namespace League\CommonMark\Tests\Unit;

use League\CommonMark\HtmlElement;

class HtmlElementTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorOneArgument()
    {
        $p = new HtmlElement('p');
        $this->assertEquals('p', $p->getTagName());
        $this->assertEmpty($p->getAllAttributes());
        $this->assertEmpty($p->getContents());
    }

    public function testConstructorTwoArguments()
    {
        $img = new HtmlElement('img', array('src' => 'foo.jpg'));
        $this->assertEquals('img', $img->getTagName());
        $this->assertCount(1, $img->getAllAttributes());
        $this->assertEquals('foo.jpg', $img->getAttribute('src'));
        $this->assertEmpty($img->getContents());
    }

    public function testConstructorThreeArguments()
    {
        $li = new HtmlElement('li', array('class' => 'odd'), 'Foo');
        $this->assertEquals('li', $li->getTagName());
        $this->assertCount(1, $li->getAllAttributes());
        $this->assertEquals('odd', $li->getAttribute('class'));
        $this->assertEquals('Foo', $li->getContents());
    }

    public function testNonSelfClosingElement()
    {
        $p = new HtmlElement('p', array(), '', false);

        $this->assertEquals('<p></p>', (string) $p);
    }

    public function testSelfClosingElement()
    {
        $hr = new HtmlElement('hr', array(), '', true);

        $this->assertEquals('<hr />', (string) $hr);
    }

    public function testGetSetExistingAttribute()
    {
        $p = new HtmlElement('p', array('class' => 'foo'));
        $this->assertCount(1, $p->getAllAttributes());
        $this->assertEquals('foo', $p->getAttribute('class'));

        $p->setAttribute('class', 'bar');
        $this->assertCount(1, $p->getAllAttributes());
        $this->assertEquals('bar', $p->getAttribute('class'));
    }

    public function testGetSetNonExistingAttribute()
    {
        $p = new HtmlElement('p', array('class' => 'foo'));
        $this->assertCount(1, $p->getAllAttributes());
        $this->assertNull($p->getAttribute('id'));

        $p->setAttribute('id', 'bar');
        $this->assertCount(2, $p->getAllAttributes());
        $this->assertEquals('bar', $p->getAttribute('id'));
        $this->assertEquals('foo', $p->getAttribute('class'));
    }

    public function testToString()
    {
        $img = new HtmlElement('img', array(), '', true);
        $p = new HtmlElement('p');
        $div = new HtmlElement('div');
        $div->setContents(array($p, $img));

        $this->assertInternalType('string', $div->getContents(true));
        $this->assertEquals('<p></p><img />', $div->getContents(true));

        $this->assertEquals('<div><p></p><img /></div>', $div->__toString());
    }

    public function testNullContentConstructor()
    {
        $img = new HtmlElement('img', array(), null);
        $this->assertTrue($img->getContents(false) === '');
    }

    public function testNullContentSetter()
    {
        $img = new HtmlElement('img');
        $img->setContents(null);
        $this->assertTrue($img->getContents(false) === '');
    }
}
