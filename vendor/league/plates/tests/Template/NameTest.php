<?php

namespace League\Plates\Template;

use org\bovigo\vfs\vfsStream;

class NameTest extends \PHPUnit_Framework_TestCase
{
    private $engine;

    public function setUp()
    {
        vfsStream::setup('templates');
        vfsStream::create(
            array(
                'template.php' => '',
                'fallback.php' => '',
                'folder' => array(
                    'template.php' => ''
                )
            )
        );

        $this->engine = new \League\Plates\Engine(vfsStream::url('templates'));
        $this->engine->addFolder('folder', vfsStream::url('templates/folder'), true);
    }

    public function testCanCreateInstance()
    {
        $this->assertInstanceOf('League\Plates\Template\Name', new Name($this->engine, 'template'));
    }

    public function testGetEngine()
    {
        $name = new Name($this->engine, 'template');

        $this->assertInstanceOf('League\Plates\Engine', $name->getEngine());
    }

    public function testGetName()
    {
        $name = new Name($this->engine, 'template');

        $this->assertEquals($name->getName(), 'template');
    }

    public function testGetFolder()
    {
        $name = new Name($this->engine, 'folder::template');
        $folder = $name->getFolder();

        $this->assertInstanceOf('League\Plates\Template\Folder', $folder);
        $this->assertEquals($name->getFolder()->getName(), 'folder');
    }

    public function testGetFile()
    {
        $name = new Name($this->engine, 'template');

        $this->assertEquals($name->getFile(), 'template.php');
    }

    public function testGetPath()
    {
        $name = new Name($this->engine, 'template');

        $this->assertEquals($name->getPath(), vfsStream::url('templates/template.php'));
    }

    public function testGetPathWithFolder()
    {
        $name = new Name($this->engine, 'folder::template');

        $this->assertEquals($name->getPath(), vfsStream::url('templates/folder/template.php'));
    }

    public function testGetPathWithFolderFallback()
    {
        $name = new Name($this->engine, 'folder::fallback');

        $this->assertEquals($name->getPath(), vfsStream::url('templates/fallback.php'));
    }

    public function testTemplateExists()
    {
        $name = new Name($this->engine, 'template');

        $this->assertEquals($name->doesPathExist(), true);
    }

    public function testTemplateDoesNotExist()
    {
        $name = new Name($this->engine, 'missing');

        $this->assertEquals($name->doesPathExist(), false);
    }

    public function testParse()
    {
        $name = new Name($this->engine, 'template');

        $this->assertEquals($name->getName(), 'template');
        $this->assertEquals($name->getFolder(), null);
        $this->assertEquals($name->getFile(), 'template.php');
    }

    public function testParseWithNoDefaultDirectory()
    {
        $this->setExpectedException('LogicException', 'The default directory has not been defined.');

        $this->engine->setDirectory(null);
        $name = new Name($this->engine, 'template');
        $name->getPath();
    }

    public function testParseWithEmptyTemplateName()
    {
        $this->setExpectedException('LogicException', 'The template name cannot be empty.');

        $name = new Name($this->engine, '');
    }

    public function testParseWithFolder()
    {
        $name = new Name($this->engine, 'folder::template');

        $this->assertEquals($name->getName(), 'folder::template');
        $this->assertEquals($name->getFolder()->getName(), 'folder');
        $this->assertEquals($name->getFile(), 'template.php');
    }

    public function testParseWithFolderAndEmptyTemplateName()
    {
        $this->setExpectedException('LogicException', 'The template name cannot be empty.');

        $name = new Name($this->engine, 'folder::');
    }

    public function testParseWithInvalidName()
    {
        $this->setExpectedException('LogicException', 'Do not use the folder namespace seperator "::" more than once.');

        $name = new Name($this->engine, 'folder::template::wrong');
    }

    public function testParseWithNoFileExtension()
    {
        $this->engine->setFileExtension(null);

        $name = new Name($this->engine, 'template.php');

        $this->assertEquals($name->getName(), 'template.php');
        $this->assertEquals($name->getFolder(), null);
        $this->assertEquals($name->getFile(), 'template.php');
    }
}
