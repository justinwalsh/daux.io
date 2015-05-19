<?php

namespace League\Plates\Template;

use org\bovigo\vfs\vfsStream;

class FolderTest extends \PHPUnit_Framework_TestCase
{
    private $folder;

    public function setUp()
    {
        vfsStream::setup('templates');

        $this->folder = new Folder('folder', vfsStream::url('templates'));
    }

    public function testCanCreateInstance()
    {
        $this->assertInstanceOf('League\Plates\Template\Folder', $this->folder);
    }

    public function testSetAndGetName()
    {
        $this->folder->setName('name');
        $this->assertEquals($this->folder->getName(), 'name');
    }

    public function testSetAndGetPath()
    {
        vfsStream::create(
            array(
                'folder' => array()
            )
        );

        $this->folder->setPath(vfsStream::url('templates/folder'));
        $this->assertEquals($this->folder->getPath(), vfsStream::url('templates/folder'));
    }

    public function testSetInvalidPath()
    {
        $this->setExpectedException('LogicException', 'The specified directory path "vfs://does/not/exist" does not exist.');
        $this->folder->setPath(vfsStream::url('does/not/exist'));
    }

    public function testSetAndGetFallback()
    {
        $this->assertEquals($this->folder->getFallback(), false);
        $this->folder->setFallback(true);
        $this->assertEquals($this->folder->getFallback(), true);
    }
}
