<?php namespace Todaymade\Daux\Tree;

use Todaymade\Daux\Config;

class ContentTest extends \PHPUnit_Framework_TestCase
{
    protected function createContent($content)
    {
        $config = new Config;
        $config->setDocumentationDirectory('');

        $dir = new Directory(new Root($config), '');
        $obj = new Content($dir, '');
        $obj->setContent($content);

        return $obj;
    }

    public function providerTestAttributes()
    {
        return [
            ['This is content', [], 'This is content'],
            ["---\ntitle: This is a simple title\n---\nThis is content\n", ['title' => 'This is a simple title'], 'This is content'],
            ["---\ntitle: This is a simple title\ntags:\n  - One\n  - Second Tag\n---\nThis is content\n", ['title' => 'This is a simple title', 'tags' => ['One', 'Second Tag']], 'This is content'],
            ['title: This is only metatada, no content', [], 'title: This is only metatada, no content'],
            ["---\ntitle: This is almost only metadata\n---\n", ['title' => 'This is almost only metadata'], ''],
            ["# Some content\n\nhi\n```yml\nvalue: true\n```\n----\n Follow up", [], "# Some content\n\nhi\n```yml\nvalue: true\n```\n----\n Follow up"],
        ];
    }

    /**
     * @dataProvider providerTestAttributes
     */
    public function testAttributes($content, $attributes, $finalContent)
    {
        $obj = $this->createContent($content);

        $this->assertEquals($attributes, $obj->getAttribute());
        $this->assertEquals($finalContent, trim($obj->getContent()));
    }

    public function testNoAttributes()
    {
        $content = "This is a content with a separator\n---\n this wasn't an attribute";

        $obj = $this->createContent($content);

        $this->assertEquals($content, $obj->getContent());
    }

    public function testContentPreserved()
    {
        $content = "this was an attribute, but also a separator\n---\nand it works";
        $with_attribute = "---\ntitle: a title\n---\n$content";

        $obj = $this->createContent($with_attribute);

        $this->assertEquals($content, $obj->getContent());
        $this->assertEquals('a title', $obj->getAttribute('title'));
    }
}
