<?php namespace Todaymade\Daux\Tree;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use Todaymade\Daux\Config;
use Todaymade\Daux\Daux;

class BuilderIntegrationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var vfsStreamDirectory
     */
    private $root;

    public function setUp()
    {
        $structure = [
            'Contents' => [
                'Page.md' => 'some text content',
            ],
            'Widgets' => [
                'Page.md' => 'another page',
                'Button.md' => 'another page',
            ],
        ];
        $this->root = vfsStream::setup('root', null, $structure);
    }

    public function testCreateHierarchy()
    {
        $config = new Config();
        $config->setDocumentationDirectory($this->root->url());
        $config['valid_content_extensions'] = ['md'];
        $config['mode'] = Daux::STATIC_MODE;
        $config['index_key'] = 'index.html';

        $tree = new Root($config);
        Builder::build($tree, []);

        $this->assertCount(2, $tree);

        $this->assertTrue(array_key_exists('Contents', $tree->getEntries()));
        $this->assertInstanceOf(Directory::class, $tree['Contents']);
        $this->assertTrue(array_key_exists('Widgets', $tree->getEntries()));
        $this->assertInstanceOf(Directory::class, $tree['Widgets']);

        // TODO :: should not be Page.html, this should not depend on the mode
        $this->assertEquals('Page', $tree['Contents']['Page.html']->getTitle());
        $this->assertInstanceOf(Content::class, $tree['Contents']['Page.html']);
    }
}
