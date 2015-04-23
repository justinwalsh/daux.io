<?php

namespace League\Plates\Template;

class FuncTest extends \PHPUnit_Framework_TestCase
{
    private $function;

    public function setUp()
    {
        $this->function = new Func('uppercase', function ($string) {
            return strtoupper($string);
        });
    }

    public function testCanCreateInstance()
    {
        $this->assertInstanceOf('League\Plates\Template\Func', $this->function);
    }

    public function testSetAndGetName()
    {
        $this->assertInstanceOf('League\Plates\Template\Func', $this->function->setName('test'));
        $this->assertEquals($this->function->getName(), 'test');
    }

    public function testSetInvalidName()
    {
        $this->setExpectedException('LogicException', 'Not a valid function name.');
        $this->function->setName('invalid-function-name');
    }

    public function testSetAndGetCallback()
    {
        $this->assertInstanceOf('League\Plates\Template\Func', $this->function->setCallback('strtolower'));
        $this->assertEquals($this->function->getCallback(), 'strtolower');
    }

    public function testSetInvalidCallback()
    {
        $this->setExpectedException('LogicException', 'Not a valid function callback.');
        $this->function->setCallback(null);
    }

    public function testFunctionCall()
    {
        $this->assertEquals($this->function->call(null, array('Jonathan')), 'JONATHAN');
    }

    public function testExtensionFunctionCall()
    {
        $extension = $this->getMock('League\Plates\Extension\ExtensionInterface', array('register', 'foo'));
        $extension->method('foo')->willReturn('bar');
        $this->function->setCallback(array($extension, 'foo'));
        $this->assertEquals($this->function->call(null), 'bar');
    }
}
