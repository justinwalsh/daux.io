<?php

namespace League\Plates\Extension;

use League\Plates\Engine;
use org\bovigo\vfs\vfsStream;

class AssetTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        vfsStream::setup('assets');
    }

    public function testCanCreateInstance()
    {
        $this->assertInstanceOf('League\Plates\Extension\Asset', new Asset(vfsStream::url('assets')));
        $this->assertInstanceOf('League\Plates\Extension\Asset', new Asset(vfsStream::url('assets'), true));
        $this->assertInstanceOf('League\Plates\Extension\Asset', new Asset(vfsStream::url('assets'), false));
    }

    public function testRegister()
    {
        $engine = new Engine();
        $extension = new Asset(vfsStream::url('assets'));
        $extension->register($engine);
        $this->assertEquals($engine->doesFunctionExist('asset'), true);
    }

    public function testCachedAssetUrl()
    {
        vfsStream::create(
            array(
                'styles.css' => ''
            )
        );

        $extension = new Asset(vfsStream::url('assets'));
        $this->assertTrue($extension->cachedAssetUrl('styles.css') === 'styles.css?v=' . filemtime(vfsStream::url('assets/styles.css')));
        $this->assertTrue($extension->cachedAssetUrl('/styles.css') === '/styles.css?v=' . filemtime(vfsStream::url('assets/styles.css')));
    }

    public function testCachedAssetUrlInFolder()
    {
        vfsStream::create(
            array(
                'folder' => array(
                    'styles.css' => ''
                )
            )
        );

        $extension = new Asset(vfsStream::url('assets'));
        $this->assertTrue($extension->cachedAssetUrl('/folder/styles.css') === '/folder/styles.css?v=' . filemtime(vfsStream::url('assets/folder/styles.css')));
    }

    public function testCachedAssetUrlUsingFilenameMethod()
    {
        vfsStream::create(
            array(
                'styles.css' => ''
            )
        );

        $extension = new Asset(vfsStream::url('assets'), true);
        $this->assertTrue($extension->cachedAssetUrl('styles.css') === 'styles.' . filemtime(vfsStream::url('assets/styles.css')) . '.css');
    }

    public function testFileNotFoundException()
    {
        $this->setExpectedException('LogicException', 'Unable to locate the asset "styles.css" in the "' . vfsStream::url('assets') . '" directory.');

        $extension = new Asset(vfsStream::url('assets'));
        $extension->cachedAssetUrl('styles.css');
    }
}
