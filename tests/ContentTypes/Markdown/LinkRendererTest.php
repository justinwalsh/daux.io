<?php namespace Todaymade\Daux\ContentTypes\Markdown;

use org\bovigo\vfs\vfsStream;
use Todaymade\Daux\Config;
use Todaymade\Daux\Daux;
use Todaymade\Daux\DauxHelper;
use Todaymade\Daux\Tree\Builder;
use Todaymade\Daux\Tree\Root;

class LinkRendererTest extends \PHPUnit_Framework_TestCase
{
    protected function getTree(Config $config)
    {
        $structure = [
            'Content' => [
                'Page.md' => 'some text content',
            ],
            'Widgets' => [
                'Page.md' => 'another page',
                'Button.md' => 'another page',
            ],
        ];
        $root = vfsStream::setup('root', null, $structure);

        $config->setDocumentationDirectory($root->url());
        $config['valid_content_extensions'] = ['md'];
        $config['mode'] = Daux::STATIC_MODE;
        $config['index_key'] = 'index.html';

        $tree = new Root($config);
        Builder::build($tree, []);

        return $tree;
    }

    public function providerRenderLink()
    {
        return [
            // /Widgets/Page
            ['<a href="http://google.ch" class="external">Link</a>', '[Link](http://google.ch)', 'Widgets/Page.html'],
            ['<a href="#features">Link</a>', '[Link](#features)', 'Widgets/Page.html'],
            ['<a href="Button.html">Link</a>', '[Link](Button.md)', 'Widgets/Page.html'],
            ['<a href="Button.html">Link</a>', '[Link](./Button.md)', 'Widgets/Page.html'],
            ['<a href="Button.html">Link</a>', '[Link](Button)', 'Widgets/Page.html'],
            ['<a href="Button.html">Link</a>', '[Link](./Button)', 'Widgets/Page.html'],
            ['<a href="Button.html">Link</a>', '[Link](!Widgets/Button)', 'Widgets/Page.html'],

            ['<a href="Button.html#Test">Link</a>', '[Link](./Button#Test)', 'Widgets/Page.html'],
            ['<a href="Button.html#Test">Link</a>', '[Link](!Widgets/Button#Test)', 'Widgets/Page.html'],

            // /Content/Page
            ['<a href="../Widgets/Button.html">Link</a>', '[Link](../Widgets/Button.md)', 'Content/Page.html'],
            ['<a href="../Widgets/Button.html">Link</a>', '[Link](!Widgets/Button)', 'Content/Page.html'],

            // Mailto links
            ['<a href="mailto:me@mydomain.com" class="external">me@mydomain.com</a>', '[me@mydomain.com](mailto:me@mydomain.com)', 'Content/Page.html'],
        ];
    }

    /**
     * @dataProvider providerRenderLink
     */
    public function testRenderLink($expected, $string, $current)
    {
        $config = new Config();
        $config['base_url'] = '';

        $config['tree'] = $this->getTree($config);
        $config->setCurrentPage(DauxHelper::getFile($config['tree'], $current));

        $converter = new CommonMarkConverter(['daux' => $config]);

        $this->assertEquals("<p>$expected</p>", trim($converter->convertToHtml($string)));
    }
}
