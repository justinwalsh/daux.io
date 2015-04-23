<?php

namespace League\Plates;

use org\bovigo\vfs\vfsStream;

class EngineTest extends \PHPUnit_Framework_TestCase
{
    private $engine;

    public function setUp()
    {
        vfsStream::setup('templates');

        $this->engine = new Engine(vfsStream::url('templates'));
    }

    public function testCanCreateInstance()
    {
        $this->assertInstanceOf('League\Plates\Engine', $this->engine);
    }

    public function testSetDirectory()
    {
        $this->assertInstanceOf('League\Plates\Engine', $this->engine->setDirectory(vfsStream::url('templates')));
        $this->assertEquals($this->engine->getDirectory(), vfsStream::url('templates'));
    }

    public function testSetNullDirectory()
    {
        $this->assertInstanceOf('League\Plates\Engine', $this->engine->setDirectory(null));
        $this->assertEquals($this->engine->getDirectory(), null);
    }

    public function testSetInvalidDirectory()
    {
        $this->setExpectedException('LogicException', 'The specified path "vfs://does/not/exist" does not exist.');
        $this->engine->setDirectory(vfsStream::url('does/not/exist'));
    }

    public function testGetDirectory()
    {
        $this->assertEquals($this->engine->getDirectory(), vfsStream::url('templates'));
    }

    public function testSetFileExtension()
    {
        $this->assertInstanceOf('League\Plates\Engine', $this->engine->setFileExtension('tpl'));
        $this->assertEquals($this->engine->getFileExtension(), 'tpl');
    }

    public function testSetNullFileExtension()
    {
        $this->assertInstanceOf('League\Plates\Engine', $this->engine->setFileExtension(null));
        $this->assertEquals($this->engine->getFileExtension(), null);
    }

    public function testGetFileExtension()
    {
        $this->assertEquals($this->engine->getFileExtension(), 'php');
    }

    public function testAddFolder()
    {
        vfsStream::create(
            array(
                'folder' => array(
                    'template.php' => ''
                )
            )
        );

        $this->assertInstanceOf('League\Plates\Engine', $this->engine->addFolder('folder', vfsStream::url('templates/folder')));
        $this->assertEquals($this->engine->getFolders()->get('folder')->getPath(), 'vfs://templates/folder');
    }

    public function testAddFolderWithNamespaceConflict()
    {
        $this->setExpectedException('LogicException', 'The template folder "name" is already being used.');
        $this->engine->addFolder('name', vfsStream::url('templates'));
        $this->engine->addFolder('name', vfsStream::url('templates'));
    }

    public function testAddFolderWithInvalidDirectory()
    {
        $this->setExpectedException('LogicException', 'The specified directory path "vfs://does/not/exist" does not exist.');
        $this->engine->addFolder('namespace', vfsStream::url('does/not/exist'));
    }

    public function testRemoveFolder()
    {
        vfsStream::create(
            array(
                'folder' => array(
                    'template.php' => ''
                )
            )
        );

        $this->engine->addFolder('folder', vfsStream::url('templates/folder'));
        $this->assertEquals($this->engine->getFolders()->exists('folder'), true);
        $this->assertInstanceOf('League\Plates\Engine', $this->engine->removeFolder('folder'));
        $this->assertEquals($this->engine->getFolders()->exists('folder'), false);
    }

    public function testGetFolders()
    {
        $this->assertInstanceOf('League\Plates\Template\Folders', $this->engine->getFolders());
    }

    public function testAddData()
    {
        $this->engine->addData(array('name' => 'Jonathan'));
        $data = $this->engine->getData();
        $this->assertEquals($data['name'], 'Jonathan');
    }

    public function testAddDataWithTemplate()
    {
        $this->engine->addData(array('name' => 'Jonathan'), 'template');
        $data = $this->engine->getData('template');
        $this->assertEquals($data['name'], 'Jonathan');
    }

    public function testAddDataWithTemplates()
    {
        $this->engine->addData(array('name' => 'Jonathan'), array('template1', 'template2'));
        $data = $this->engine->getData('template1');
        $this->assertEquals($data['name'], 'Jonathan');
    }

    public function testRegisterFunction()
    {
        vfsStream::create(
            array(
                'template.php' => '<?=$this->uppercase($name)?>'
            )
        );

        $this->engine->registerFunction('uppercase', 'strtoupper');
        $this->assertInstanceOf('League\Plates\Template\Func', $this->engine->getFunction('uppercase'));
        $this->assertEquals($this->engine->getFunction('uppercase')->getCallback(), 'strtoupper');
    }

    public function testDropFunction()
    {
        $this->engine->registerFunction('uppercase', 'strtoupper');
        $this->assertEquals($this->engine->doesFunctionExist('uppercase'), true);
        $this->engine->dropFunction('uppercase');
        $this->assertEquals($this->engine->doesFunctionExist('uppercase'), false);
    }

    public function testDropInvalidFunction()
    {
        $this->setExpectedException('LogicException', 'The template function "some_function_that_does_not_exist" was not found.');
        $this->engine->dropFunction('some_function_that_does_not_exist');
    }

    public function testGetFunction()
    {
        $this->engine->registerFunction('uppercase', 'strtoupper');
        $function = $this->engine->getFunction('uppercase');

        $this->assertInstanceOf('League\Plates\Template\Func', $function);
        $this->assertEquals($function->getName(), 'uppercase');
        $this->assertEquals($function->getCallback(), 'strtoupper');
    }

    public function testGetInvalidFunction()
    {
        $this->setExpectedException('LogicException', 'The template function "some_function_that_does_not_exist" was not found.');
        $this->engine->getFunction('some_function_that_does_not_exist');
    }

    public function testDoesFunctionExist()
    {
        $this->engine->registerFunction('uppercase', 'strtoupper');
        $this->assertEquals($this->engine->doesFunctionExist('uppercase'), true);
    }

    public function testDoesFunctionNotExist()
    {
        $this->assertEquals($this->engine->doesFunctionExist('some_function_that_does_not_exist'), false);
    }

    public function testLoadExtension()
    {
        $this->assertEquals($this->engine->doesFunctionExist('uri'), false);
        $this->assertInstanceOf('League\Plates\Engine', $this->engine->loadExtension(new \League\Plates\Extension\URI('')));
        $this->assertEquals($this->engine->doesFunctionExist('uri'), true);
    }

    public function testLoadExtensions()
    {
        $this->assertEquals($this->engine->doesFunctionExist('uri'), false);
        $this->assertEquals($this->engine->doesFunctionExist('asset'), false);
        $this->assertInstanceOf(
            'League\Plates\Engine',
            $this->engine->loadExtensions(
                array(
                    new \League\Plates\Extension\URI(''),
                    new \League\Plates\Extension\Asset('public')
                )
            )
        );
        $this->assertEquals($this->engine->doesFunctionExist('uri'), true);
        $this->assertEquals($this->engine->doesFunctionExist('asset'), true);
    }

    public function testGetTemplatePath()
    {
        $this->assertEquals($this->engine->path('template'), 'vfs://templates/template.php');
    }

    public function testTemplateExists()
    {
        $this->assertEquals($this->engine->exists('template'), false);

        vfsStream::create(
            array(
                'template.php' => ''
            )
        );

        $this->assertEquals($this->engine->exists('template'), true);
    }

    public function testMakeTemplate()
    {
        vfsStream::create(
            array(
                'template.php' => ''
            )
        );

        $this->assertInstanceOf('League\Plates\Template\Template', $this->engine->make('template'));
    }

    public function testRenderTemplate()
    {
        vfsStream::create(
            array(
                'template.php' => 'Hello!'
            )
        );

        $this->assertEquals($this->engine->render('template'), 'Hello!');
    }
}
