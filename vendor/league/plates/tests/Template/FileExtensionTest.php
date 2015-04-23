<?php

namespace League\Plates\Template;

class FileExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $fileExtension;

    public function setUp()
    {
        $this->fileExtension = new FileExtension();
    }

    public function testCanCreateInstance()
    {
        $this->assertInstanceOf('League\Plates\Template\FileExtension', $this->fileExtension);
    }

    public function testSetFileExtension()
    {
        $this->assertInstanceOf('League\Plates\Template\FileExtension', $this->fileExtension->set('tpl'));
        $this->assertEquals($this->fileExtension->get(), 'tpl');
    }

    public function testSetNullFileExtension()
    {
        $this->assertInstanceOf('League\Plates\Template\FileExtension', $this->fileExtension->set(null));
        $this->assertEquals($this->fileExtension->get(), null);
    }

    public function testGetFileExtension()
    {
        $this->assertEquals($this->fileExtension->get(), 'php');
    }
}
