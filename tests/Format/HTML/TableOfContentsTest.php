<?php namespace Todaymade\Daux\Format\HTML;

use Todaymade\Daux\Config as MainConfig;
use \Todaymade\Daux\Format\HTML\ContentTypes\Markdown\CommonMarkConverter;

class TableOfContentsTest extends \PHPUnit_Framework_TestCase
{
    function testNoTOCByDefault() {
        $converter = new CommonMarkConverter(['daux' => new MainConfig]);

        $this->assertEquals("<h1 id=\"page_Test\">Test</h1>\n", $converter->convertToHtml('# Test'));
    }

    function testTOCToken() {
        $converter = new CommonMarkConverter(['daux' => new MainConfig]);

        $source = "[TOC]\n# Title";
        $expected = <<<EXPECTED
<ul class="TableOfContents">
<li>
<p><a href="#page_Title">Title</a></p>
</li>
</ul>
<h1 id="page_Title">Title</h1>

EXPECTED;

        $this->assertEquals($expected, $converter->convertToHtml($source));
    }

    function testUnicodeTOC() {
        $converter = new CommonMarkConverter(['daux' => new MainConfig]);

        $source = "[TOC]\n# 基础操作";
        $expected = <<<EXPECTED
<ul class="TableOfContents">
<li>
<p><a href="#page_%E5%9F%BA%E7%A1%80%E6%93%8D%E4%BD%9C">基础操作</a></p>
</li>
</ul>
<h1 id="page_%E5%9F%BA%E7%A1%80%E6%93%8D%E4%BD%9C">基础操作</h1>

EXPECTED;

        $this->assertEquals($expected, $converter->convertToHtml($source));
    }
}


