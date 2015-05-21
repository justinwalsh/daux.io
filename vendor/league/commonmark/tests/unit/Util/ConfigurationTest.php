<?php

namespace League\CommonMark\Tests\Unit\Util;

use League\CommonMark\Util\Configuration;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testGetConfig()
    {
        $data = [
            'foo' => 'bar',
            'a' => array(
                'b' => 'c',
            ),
        ];

        $config = new Configuration($data);

        // No arguments should return the whole thing
        $this->assertEquals($data, $config->getConfig());

        // Test getting a single scalar element
        $this->assertEquals('bar', $config->getConfig('foo'));

        // Test getting a single array element
        $this->assertEquals($data['a'], $config->getConfig('a'));

        // Test getting an element by path
        $this->assertEquals('c', $config->getConfig('a/b'));

        // Test getting a path that's one level too deep
        $this->assertNull($config->getConfig('a/b/c'));

        // Test getting a non-existent element
        $this->assertNull($config->getConfig('test'));

        // Test getting a non-existent element with a default value
        $this->assertEquals(42, $config->getConfig('answer', 42));
    }

    public function testSetConfig()
    {
        $config = new Configuration(array('foo' => 'bar'));
        $config->setConfig(array('test' => '123'));

        $this->assertNull($config->getConfig('foo'));
        $this->assertEquals('123', $config->getConfig('test'));
    }

    public function testMergeConfig()
    {
        $config = new Configuration(array('foo' => 'bar', 'test' => '123'));
        $config->mergeConfig(array('test' => '456'));

        $this->assertEquals('bar', $config->getConfig('foo'));
        $this->assertEquals('456', $config->getConfig('test'));
    }
}
