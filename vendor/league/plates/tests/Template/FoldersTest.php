<?php

namespace League\Plates\Template;

use org\bovigo\vfs\vfsStream;

class FoldersTest extends \PHPUnit_Framework_TestCase
{
    private $folders;

    public function setUp()
    {
        vfsStream::setup('templates');

        $this->folders = new Folders();
    }

    public function testCanCreateInstance()
    {
        $this->assertInstanceOf('League\Plates\Template\Folders', $this->folders);
    }

    public function testAddFolder()
    {
        $this->assertInstanceOf('League\Plates\Template\Folders', $this->folders->add('name', vfsStream::url('templates')));
        $this->assertEquals($this->folders->get('name')->getPath(), 'vfs://templates');
    }

    public function testAddFolderWithNamespaceConflict()
    {
        $this->setExpectedException('LogicException', 'The template folder "name" is already being used.');
        $this->folders->add('name', vfsStream::url('templates'));
        $this->folders->add('name', vfsStream::url('templates'));
    }

    public function testAddFolderWithInvalidDirectory()
    {
        $this->setExpectedException('LogicException', 'The specified directory path "vfs://does/not/exist" does not exist.');
        $this->folders->add('name', vfsStream::url('does/not/exist'));
    }

    public function testRemoveFolder()
    {
        $this->folders->add('folder', vfsStream::url('templates'));
        $this->assertEquals($this->folders->exists('folder'), true);
        $this->assertInstanceOf('League\Plates\Template\Folders', $this->folders->remove('folder'));
        $this->assertEquals($this->folders->exists('folder'), false);
    }

    public function testRemoveFolderWithInvalidDirectory()
    {
        $this->setExpectedException('LogicException', 'The template folder "name" was not found.');
        $this->folders->remove('name');
    }

    public function testGetFolder()
    {
        $this->folders->add('name', vfsStream::url('templates'));
        $this->assertInstanceOf('League\Plates\Template\Folder', $this->folders->get('name'));
        $this->assertEquals($this->folders->get('name')->getPath(), vfsStream::url('templates'));
    }

    public function testGetNonExistentFolder()
    {
        $this->setExpectedException('LogicException', 'The template folder "name" was not found.');
        $this->assertInstanceOf('League\Plates\Template\Folder', $this->folders->get('name'));
    }

    public function testFolderExists()
    {
        $this->assertEquals($this->folders->exists('name'), false);
        $this->folders->add('name', vfsStream::url('templates'));
        $this->assertEquals($this->folders->exists('name'), true);
    }
}
