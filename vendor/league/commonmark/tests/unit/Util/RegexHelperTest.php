<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Util;

use League\CommonMark\Util\RegexHelper;

/**
 * Tests the different regular expressions
 */
class RegexHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RegexHelper
     */
    protected $regexHelper;

    public function setUp()
    {
        $this->regexHelper = RegexHelper::getInstance();
    }

    public function testEscapable()
    {
        $regex = '/^' . $this->regexHelper->getPartialRegex(RegexHelper::ESCAPABLE) . '$/';
        $this->assertRegExp($regex, '&');
        $this->assertRegExp($regex, '/');
        $this->assertRegExp($regex, '\\');
        $this->assertRegExp($regex, '(');
        $this->assertRegExp($regex, ')');
    }

    public function testEscapedChar()
    {
        $regex = '/^' . $this->regexHelper->getPartialRegex(RegexHelper::ESCAPED_CHAR) . '$/';
        $this->assertRegExp($regex, '\\&');
        $this->assertRegExp($regex, '\\/');
        $this->assertRegExp($regex, '\\\\');
        $this->assertRegExp($regex, '\)');
        $this->assertRegExp($regex, '\(');
    }

    public function testInDoubleQuotes()
    {
        $regex = '/^' . $this->regexHelper->getPartialRegex(RegexHelper::IN_DOUBLE_QUOTES) . '$/';
        $this->assertRegExp($regex, '"\\&"');
        $this->assertRegExp($regex, '"\\/"');
        $this->assertRegExp($regex, '"\\\\"');
    }

    public function testInSingleQuotes()
    {
        $regex = '/^' . $this->regexHelper->getPartialRegex(RegexHelper::IN_SINGLE_QUOTES) . '$/';
        $this->assertRegExp($regex, '\'\\&\'');
        $this->assertRegExp($regex, '\'\\/\'');
        $this->assertRegExp($regex, '\'\\\\\'');
    }

    public function testInParens()
    {
        $regex = '/^' . $this->regexHelper->getPartialRegex(RegexHelper::IN_PARENS) . '$/';
        $this->assertRegExp($regex, '(\\&)');
        $this->assertRegExp($regex, '(\\/)');
        $this->assertRegExp($regex, '(\\\\)');
    }

    public function testRegChar()
    {
        $regex = '/^' . $this->regexHelper->getPartialRegex(RegexHelper::REG_CHAR) . '$/';
        $this->assertRegExp($regex, 'a');
        $this->assertRegExp($regex, 'A');
        $this->assertRegExp($regex, '!');
        $this->assertNotRegExp($regex, ' ');
    }

    public function testInParensNoSp()
    {
        $regex = '/^' . $this->regexHelper->getPartialRegex(RegexHelper::IN_PARENS_NOSP) . '$/';
        $this->assertRegExp($regex, '(a)');
        $this->assertRegExp($regex, '(A)');
        $this->assertRegExp($regex, '(!)');
        $this->assertNotRegExp($regex, '(a )');
    }

    public function testTagname()
    {
        $regex = '/^' . $this->regexHelper->getPartialRegex(RegexHelper::TAGNAME) . '$/';
        $this->assertRegExp($regex, 'a');
        $this->assertRegExp($regex, 'img');
        $this->assertRegExp($regex, 'h1');
        $this->assertNotRegExp($regex, '11');
    }

    public function testBlockTagName()
    {
        $regex = '/^' . $this->regexHelper->getPartialRegex(RegexHelper::BLOCKTAGNAME) . '$/';
        $this->assertRegExp($regex, 'p');
        $this->assertRegExp($regex, 'div');
        $this->assertRegExp($regex, 'h1');
        $this->assertNotRegExp($regex, 'a');
        $this->assertNotRegExp($regex, 'h7');
    }

    public function testAttributeName()
    {
        $regex = '/^' . $this->regexHelper->getPartialRegex(RegexHelper::ATTRIBUTENAME) . '$/';
        $this->assertRegExp($regex, 'href');
        $this->assertRegExp($regex, 'class');
        $this->assertRegExp($regex, 'data-src');
        $this->assertNotRegExp($regex, '-key');
    }

    public function testUnquotedValue()
    {
        $regex = '/^' . $this->regexHelper->getPartialRegex(RegexHelper::UNQUOTEDVALUE) . '$/';
        $this->assertRegExp($regex, 'foo');
        $this->assertRegExp($regex, 'bar');
        $this->assertNotRegExp($regex, '"baz"');
    }

    public function testSingleQuotedValue()
    {
        $regex = '/^' . $this->regexHelper->getPartialRegex(RegexHelper::SINGLEQUOTEDVALUE) . '$/';
        $this->assertRegExp($regex, '\'foo\'');
        $this->assertRegExp($regex, '\'bar\'');
        $this->assertNotRegExp($regex, '"baz"');
    }

    public function testDoubleQuotedValue()
    {
        $regex = '/^' . $this->regexHelper->getPartialRegex(RegexHelper::DOUBLEQUOTEDVALUE) . '$/';
        $this->assertRegExp($regex, '"foo"');
        $this->assertRegExp($regex, '"bar"');
        $this->assertNotRegExp($regex, '\'baz\'');
    }

    public function testAttributeValue()
    {
        $regex = '/^' . $this->regexHelper->getPartialRegex(RegexHelper::ATTRIBUTEVALUE) . '$/';
        $this->assertRegExp($regex, 'foo');
        $this->assertRegExp($regex, '\'bar\'');
        $this->assertRegExp($regex, '"baz"');
    }

    public function testAttributeValueSpec()
    {
        $regex = '/^' . $this->regexHelper->getPartialRegex(RegexHelper::ATTRIBUTEVALUESPEC) . '$/';
        $this->assertRegExp($regex, '=foo');
        $this->assertRegExp($regex, '= foo');
        $this->assertRegExp($regex, ' =foo');
        $this->assertRegExp($regex, ' = foo');
        $this->assertRegExp($regex, '=\'bar\'');
        $this->assertRegExp($regex, '= \'bar\'');
        $this->assertRegExp($regex, ' =\'bar\'');
        $this->assertRegExp($regex, ' = \'bar\'');
        $this->assertRegExp($regex, '="baz"');
        $this->assertRegExp($regex, '= "baz"');
        $this->assertRegExp($regex, ' ="baz"');
        $this->assertRegExp($regex, ' = "baz"');
    }

    public function testAttribute()
    {
        $regex = '/^' . $this->regexHelper->getPartialRegex(RegexHelper::ATTRIBUTE) . '$/';
        $this->assertRegExp($regex, ' disabled');
        $this->assertRegExp($regex, ' disabled="disabled"');
        $this->assertRegExp($regex, ' href="http://www.google.com"');
        $this->assertNotRegExp($regex, 'disabled', 'There must be at least one space at the start');
    }

    public function testOpenTag()
    {
        $regex = '/^' . $this->regexHelper->getPartialRegex(RegexHelper::OPENTAG) . '$/';
        $this->assertRegExp($regex, '<hr>');
        $this->assertRegExp($regex, '<a href="http://www.google.com">');
        $this->assertRegExp($regex, '<img src="http://www.google.com/logo.png" />');
        $this->assertNotRegExp($regex, '</p>');
    }

    public function testCloseTag()
    {
        $regex = '/^' . $this->regexHelper->getPartialRegex(RegexHelper::CLOSETAG) . '$/';
        $this->assertRegExp($regex, '</p>');
        $this->assertRegExp($regex, '</a>');
        $this->assertNotRegExp($regex, '<hr>');
        $this->assertNotRegExp($regex, '<img src="http://www.google.com/logo.png" />');
    }

    public function testOpenBlockTag()
    {
        $regex = '/^' . $this->regexHelper->getPartialRegex(RegexHelper::OPENBLOCKTAG) . '$/';
        $this->assertRegExp($regex, '<body>');
        $this->assertRegExp($regex, '<hr>');
        $this->assertRegExp($regex, '<hr />');
        $this->assertRegExp($regex, '<p id="foo" class="bar">');
        $this->assertNotRegExp($regex, '<a href="http://www.google.com">', 'This is not a block element');
        $this->assertNotRegExp($regex, '</p>', 'This is not an opening tag');
    }

    public function testCloseBlockTag()
    {
        $regex = '/^' . $this->regexHelper->getPartialRegex(RegexHelper::CLOSEBLOCKTAG) . '$/';
        $this->assertRegExp($regex, '</body>');
        $this->assertRegExp($regex, '</p>');
        $this->assertNotRegExp($regex, '</a>', 'This is not a block element');
        $this->assertNotRegExp($regex, '<br>', 'This is not a closing tag');
    }

    public function testHtmlComment()
    {
        $regex = '/^' . $this->regexHelper->getPartialRegex(RegexHelper::HTMLCOMMENT) . '$/';
        $this->assertRegExp($regex, '<!---->');
        $this->assertRegExp($regex, '<!-- -->');
        $this->assertRegExp($regex, '<!-- HELLO WORLD -->');
        $this->assertNotRegExp($regex, '<!->');
        $this->assertNotRegExp($regex, '<!-->');
        $this->assertNotRegExp($regex, '<!--->');
        $this->assertNotRegExp($regex, '<!- ->');
    }

    public function testProcessingInstruction()
    {
        $regex = '/^' . $this->regexHelper->getPartialRegex(RegexHelper::PROCESSINGINSTRUCTION) . '$/';
        $this->assertRegExp($regex, '<?PITarget PIContent?>');
        $this->assertRegExp($regex, '<?xml-stylesheet type="text/xsl" href="style.xsl"?>');
    }

    public function testDeclaration()
    {
        $regex = '/^' . $this->regexHelper->getPartialRegex(RegexHelper::DECLARATION) . '$/';
        $this->assertRegExp($regex, '<!DOCTYPE html>');
        $this->assertRegExp($regex, '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">');
        $this->assertRegExp($regex, '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">');
    }

    public function testCDATA()
    {
        $regex = '/^' . $this->regexHelper->getPartialRegex(RegexHelper::CDATA) . '$/';
        $this->assertRegExp($regex, '<![CDATA[<sender>John Smith</sender>]]>');
        $this->assertRegExp($regex, '<![CDATA[]]]]><![CDATA[>]]>');
    }

    public function testHtmlTag()
    {
        $regex = '/^' . $this->regexHelper->getPartialRegex(RegexHelper::HTMLTAG) . '$/';
        $this->assertRegExp($regex, '<body id="main">');
        $this->assertRegExp($regex, '</p>');
        $this->assertRegExp($regex, '<!-- HELLO WORLD -->');
        $this->assertRegExp($regex, '<?xml-stylesheet type="text/xsl" href="style.xsl"?>');
        $this->assertRegExp($regex, '<!DOCTYPE html>');
        $this->assertRegExp($regex, '<![CDATA[<sender>John Smith</sender>]]>');
    }

    public function testHtmlBlockOpen()
    {
        $regex = '/^' . $this->regexHelper->getPartialRegex(RegexHelper::HTMLBLOCKOPEN) . '$/';
        $this->assertRegExp($regex, '<h1>');
        $this->assertRegExp($regex, '</p>');
    }

    public function testLinkTitle()
    {
        $regex = '/^' . $this->regexHelper->getPartialRegex(RegexHelper::HTMLBLOCKOPEN) . '$/';
        $this->assertRegExp($regex, '<h1>');
        $this->assertRegExp($regex, '</p>');
    }

    public function testUnescape()
    {
        $this->assertEquals('foo(and(bar))', RegexHelper::unescape('foo(and\\(bar\\))'));
    }

    /**
     * @param $regex
     * @param $string
     * @param $offset
     * @param $expectedResult
     *
     * @dataProvider dataForTestMatchAt
     */
    public function testMatchAt($regex, $string, $offset, $expectedResult)
    {
        $this->assertEquals($expectedResult, RegexHelper::matchAt($regex, $string, $offset));
    }

    /**
     * @return array
     */
    public function dataForTestMatchAt()
    {
        return array(
            array('/ /', 'foo bar', null, 3),
            array('/ /', 'foo bar', 0, 3),
            array('/ /', 'foo bar', 1, 3),
            array('/ /', 'это тест', null, 3),
            array('/ /', 'это тест', 0, 3),
            array('/ /', 'это тест', 1, 3),
        );
    }

}
