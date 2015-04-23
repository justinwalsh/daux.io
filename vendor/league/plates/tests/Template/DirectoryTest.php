<?php

namespace League\Plates\Template;

use org\bovigo\vfs\vfsStream;

class DirectoryTest extends \PHPUnit_Framework_TestCase
{
    private $directory;

    public function setUp()
    {
        vfsStream::setup('templates');

        $this->directory = new Directory();
    }

    public function testCanCreateInstance()
    {
        $this->assertInstanceOf('League\Plates\Template\Directory', $this->directory);
    }

    public function testSetDirectory()
    {
        $this->assertInstanceOf('League\Plates\Template\Directory', $this->directory->set(vfsStream::url('templates')));
        $this->assertEquals($this->directory->get(), vfsStream::url('templates'));
    }

    public function testSetNullDirectory()
    {
        $this->assertInstanceOf('League\Plates\Template\Directory', $this->directory->set(null));
        $this->assertEquals($this->directory->get(), null);
    }

    public function testSetInvalidDirectory()
    {
        $this->setExpectedException('LogicException', 'The specified path "vfs://does/not/exist" does not exist.');
        $this->directory->set(vfsStream::url('does/not/exist'));
    }

    public function testGetDirectory()
    {
        $this->assertEquals($this->directory->get(), null);
    }
}
