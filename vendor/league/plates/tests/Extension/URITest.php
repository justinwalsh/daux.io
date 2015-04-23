<?php

namespace League\Plates\Extension;

use League\Plates\Engine;

class URITest extends \PHPUnit_Framework_TestCase
{
    private $extension;

    public function setUp()
    {
        $this->extension = new URI('/green/red/blue');
    }

    public function testCanCreateInstance()
    {
        $this->assertInstanceOf('League\Plates\Extension\URI', $this->extension);
    }

    public function testRegister()
    {
        $engine = new Engine();
        $extension = new URI('/green/red/blue');
        $extension->register($engine);
        $this->assertEquals($engine->doesFunctionExist('uri'), true);
    }

    public function testGetUrl()
    {
        $this->assertTrue($this->extension->runUri() === '/green/red/blue');
    }

    public function testGetSpecifiedSegment()
    {
        $this->assertTrue($this->extension->runUri(1) === 'green');
        $this->assertTrue($this->extension->runUri(2) === 'red');
        $this->assertTrue($this->extension->runUri(3) === 'blue');
    }

    public function testSegmentMatch()
    {
        $this->assertTrue($this->extension->runUri(1, 'green'));
        $this->assertFalse($this->extension->runUri(1, 'red'));
    }

    public function testSegmentMatchSuccessResponse()
    {
        $this->assertTrue($this->extension->runUri(1, 'green', 'success') === 'success');
    }

    public function testSegmentMatchFailureResponse()
    {
        $this->assertFalse($this->extension->runUri(1, 'red', 'success'));
    }

    public function testSegmentMatchFailureCustomResponse()
    {
        $this->assertTrue($this->extension->runUri(1, 'red', 'success', 'fail') === 'fail');
    }

    public function testRegexMatch()
    {
        $this->assertTrue($this->extension->runUri('/[a-z]+/red/blue'));
    }

    public function testRegexMatchSuccessResponse()
    {
        $this->assertTrue($this->extension->runUri('/[a-z]+/red/blue', 'success') === 'success');
    }

    public function testRegexMatchFailureResponse()
    {
        $this->assertFalse($this->extension->runUri('/[0-9]+/red/blue', 'success'));
    }

    public function testRegexMatchFailureCustomResponse()
    {
        $this->assertTrue($this->extension->runUri('/[0-9]+/red/blue', 'success', 'fail') === 'fail');
    }

    public function testInvalidCall()
    {
        $this->setExpectedException('LogicException', 'Invalid use of the uri function.');

        $this->extension->runUri(array());
    }
}
