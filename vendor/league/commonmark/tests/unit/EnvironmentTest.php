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

use League\CommonMark\Environment;
use League\CommonMark\InlineParserEngine;

class EnvironmentTest extends \PHPUnit_Framework_TestCase
{
    public function testAddGetExtensions()
    {
        $extension = $this->getMockForAbstractClass('League\CommonMark\Extension\ExtensionInterface');
        $extension->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('foo'));

        $environment = new Environment();
        $this->assertCount(0, $environment->getExtensions());

        $environment->addExtension($extension);

        $extensions = $environment->getExtensions();
        $this->assertCount(1, $extensions);
        $this->assertEquals($extension, $extensions['foo']);
    }

    public function testConstructor()
    {
        $config = array('foo' => 'bar');
        $environment = new Environment($config);
        $this->assertEquals('bar', $environment->getConfig('foo'));
    }

    public function testGetConfig()
    {
        $config = array(
            'foo' => 'bar',
            'a' => array(
                'b' => 'c'
            )
        );
        $environment = new Environment($config);

        // No arguments should return the whole thing
        $this->assertEquals($config, $environment->getConfig());

        // Test getting a single scalar element
        $this->assertEquals('bar', $environment->getConfig('foo'));

        // Test getting a single array element
        $this->assertEquals($config['a'], $environment->getConfig('a'));

        // Test getting an element by path
        $this->assertEquals('c', $environment->getConfig('a/b'));

        // Test getting a path that's one level too deep
        $this->assertNull($environment->getConfig('a/b/c'));

        // Test getting a non-existent element
        $this->assertNull($environment->getConfig('test'));

        // Test getting a non-existent element with a default value
        $this->assertEquals(42, $environment->getConfig('answer', 42));
    }

    public function testSetConfig()
    {
        $environment = new Environment(array('foo' => 'bar'));
        $environment->setConfig(array('test' => '123'));
        $this->assertNull($environment->getConfig('foo'));
        $this->assertEquals('123', $environment->getConfig('test'));
    }

    public function testSetConfigAfterInit()
    {
        $this->setExpectedException('RuntimeException');

        $environment = new Environment();
        // This triggers the initialization
        $environment->getBlockParsers();
        $environment->setConfig(array('foo' => 'bar'));
    }

    public function testMergeConfig()
    {
        $environment = new Environment(array('foo' => 'bar', 'test' => '123'));
        $environment->mergeConfig(array('test' => '456'));
        $this->assertEquals('bar', $environment->getConfig('foo'));
        $this->assertEquals('456', $environment->getConfig('test'));
    }

    public function testMergeConfigAfterInit()
    {
        $this->setExpectedException('RuntimeException');

        $environment = new Environment();
        // This triggers the initialization
        $environment->getBlockParsers();
        $environment->mergeConfig(array('foo' => 'bar'));
    }

    public function testAddBlockParserAndGetter()
    {
        $environment = new Environment();

        $parser = $this->getMock('League\CommonMark\Block\Parser\BlockParserInterface');
        $environment->addBlockParser($parser);

        $this->assertContains($parser, $environment->getBlockParsers());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testAddBlockParserFailsAfterInitialization()
    {
        $environment = new Environment();

        // This triggers the initialization
        $environment->getBlockParsers();

        $parser = $this->getMock('League\CommonMark\Block\Parser\BlockParserInterface');
        $environment->addBlockParser($parser);
    }

    public function testAddBlockRenderer()
    {
        $environment = new Environment();

        $renderer = $this->getMock('League\CommonMark\Block\Renderer\BlockRendererInterface');
        $environment->addBlockRenderer('MyClass', $renderer);

        $this->assertEquals($renderer, $environment->getBlockRendererForClass('MyClass'));
    }

    public function testSwapBuiltInBlockRenderer()
    {
        $environment = new Environment();

        $mockRenderer = $this->getMock('League\CommonMark\Block\Renderer\BlockRendererInterface');

        $builtInClasses = [
            'BlockQuote'     => 'League\CommonMark\Block\Element\BlockQuote',
            'Document'       => 'League\CommonMark\Block\Element\Document',
            'FencedCode'     => 'League\CommonMark\Block\Element\FencedCode',
            'Header'         => 'League\CommonMark\Block\Element\Header',
            'HorizontalRule' => 'League\CommonMark\Block\Element\HorizontalRule',
            'HtmlBlock'      => 'League\CommonMark\Block\Element\HtmlBlock',
            'IndentedCode'   => 'League\CommonMark\Block\Element\IndentedCode',
            'ListBlock'      => 'League\CommonMark\Block\Element\ListBlock',
            'ListItem'       => 'League\CommonMark\Block\Element\ListItem',
            'Paragraph'      => 'League\CommonMark\Block\Element\Paragraph',
        ];

        foreach ($builtInClasses as $name => $fullyQualifiedName){
            $environment->addBlockRenderer($name, $mockRenderer);
        }

        foreach ($builtInClasses as $name => $fullyQualifiedName){
            $this->assertEquals(
                $mockRenderer,
                $environment->getBlockRendererForClass($fullyQualifiedName)
            );
        }
    }

    public function testSwapBuiltInInlineRenderer()
    {
        $environment = new Environment();

        $mockRenderer = $this->getMock('League\CommonMark\Inline\Renderer\InlineRendererInterface');

        $builtInClasses = [
            'Code'     => 'League\CommonMark\Inline\Element\Code',
            'Emphasis' => 'League\CommonMark\Inline\Element\Emphasis',
            'Html'     => 'League\CommonMark\Inline\Element\Html',
            'Image'    => 'League\CommonMark\Inline\Element\Image',
            'Link'     => 'League\CommonMark\Inline\Element\Link',
            'Newline'  => 'League\CommonMark\Inline\Element\Newline',
            'Strong'   => 'League\CommonMark\Inline\Element\Strong',
            'Text'     => 'League\CommonMark\Inline\Element\Text',
        ];

        foreach ($builtInClasses as $name => $fullyQualifiedName){
            $environment->addInlineRenderer($name, $mockRenderer);
        }

        foreach ($builtInClasses as $name => $fullyQualifiedName){
            $this->assertEquals(
                $mockRenderer,
                $environment->getInlineRendererForClass($fullyQualifiedName)
            );
        }
    }


    /**
     * @expectedException \RuntimeException
     */
    public function testAddBlockRendererFailsAfterInitialization()
    {
        $environment = new Environment();

        // This triggers the initialization
        $environment->getBlockRendererForClass('MyClass');

        $renderer = $this->getMock('League\CommonMark\Block\Renderer\BlockRendererInterface');
        $environment->addBlockRenderer('MyClass', $renderer);
    }

    public function testAddInlineParserAndGetter()
    {
        $environment = new Environment();

        $parser = $this->getMock('League\CommonMark\Inline\Parser\InlineParserInterface');
        $parser->expects($this->any())
            ->method('getCharacters')
            ->will($this->returnValue(['a']));

        $environment->addInlineParser($parser);

        $this->assertContains($parser, $environment->getInlineParsers());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testAddInlineParserFailsAfterInitialization()
    {
        $environment = new Environment();

        // This triggers the initialization
        $environment->getInlineParsers();

        $parser = $this->getMock('League\CommonMark\Inline\Parser\InlineParserInterface');
        $environment->addInlineParser($parser);
    }

    public function testGetInlineParserByName()
    {
        $environment = new Environment();

        $parser = $this->getMock('League\CommonMark\Inline\Parser\InlineParserInterface');
        $parser->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('test'));
        $parser->expects($this->any())
            ->method('getCharacters')
            ->will($this->returnValue(['a']));

        $environment->addInlineParser($parser);

        $this->assertEquals($parser, $environment->getInlineParser('test'));
    }

    public function testGetInlineParsersForCharacter()
    {
        $environment = new Environment();

        $parser = $this->getMock('League\CommonMark\Inline\Parser\InlineParserInterface');
        $parser->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('test'));
        $parser->expects($this->any())
            ->method('getCharacters')
            ->will($this->returnValue(['a']));

        $environment->addInlineParser($parser);

        $this->assertContains($parser, $environment->getInlineParsersForCharacter('a'));
    }

    public function testGetInlineParsersForNonExistantCharacter()
    {
        $environment = new Environment();

        $this->assertNull($environment->getInlineParsersForCharacter('a'));
    }

    public function testAddInlineProcessor()
    {
        $environment = new Environment();

        $processor = $this->getMock('League\CommonMark\Inline\Processor\InlineProcessorInterface');
        $environment->addInlineProcessor($processor);

        $this->assertContains($processor, $environment->getInlineProcessors());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testAddInlineProcessorFailsAfterInitialization()
    {
        $environment = new Environment();

        // This triggers the initialization
        $environment->getInlineProcessors();

        $processor = $this->getMock('League\CommonMark\Inline\Processor\InlineProcessorInterface');
        $environment->addInlineProcessor($processor);
    }

    public function testAddInlineRenderer()
    {
        $environment = new Environment();

        $renderer = $this->getMock('League\CommonMark\Inline\Renderer\InlineRendererInterface');
        $environment->addInlineRenderer('MyClass', $renderer);

        $this->assertEquals($renderer, $environment->getInlineRendererForClass('MyClass'));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testAddInlineRendererFailsAfterInitialization()
    {
        $environment = new Environment();

        // This triggers the initialization
        $environment->getInlineRendererForClass('MyClass');

        $renderer = $this->getMock('League\CommonMark\Inline\Renderer\InlineRendererInterface');
        $environment->addInlineRenderer('MyClass', $renderer);
    }

    public function testGetBlockRendererForNonExistantClass()
    {
        $environment = new Environment();

        $renderer = $environment->getBlockRendererForClass('MyClass');

        $this->assertNull($renderer);
    }

    public function testGetInlineRendererForNonExistantClass()
    {
        $environment = new Environment();

        $renderer = $environment->getInlineRendererForClass('MyClass');

        $this->assertNull($renderer);
    }

    public function testCreateInlineParserEngine()
    {
        $environment = new Environment();

        $engine = $environment->createInlineParserEngine();

        $this->assertTrue($engine instanceof InlineParserEngine);
    }

    public function testAddExtensionAndGetter()
    {
        $environment = new Environment();

        $extension = $this->getMock('League\CommonMark\Extension\ExtensionInterface');
        $environment->addExtension($extension);

        $this->assertContains($extension, $environment->getExtensions());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testAddExtensionFailsAfterInitialization()
    {
        $environment = new Environment();

        // This triggers the initialization
        $environment->getInlineRendererForClass('MyClass');

        $extension = $this->getMock('League\CommonMark\Extension\ExtensionInterface');
        $environment->addExtension($extension);
    }
}
