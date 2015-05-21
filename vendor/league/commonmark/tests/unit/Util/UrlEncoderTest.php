<?php

namespace League\CommonMark\Tests\Unit\Util;

use League\CommonMark\Util\UrlEncoder;

class UrlEncoderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider unescapeAndEncodeTestProvider
     */
    public function testUnescapeAndEncode($input, $expected)
    {
        $this->assertEquals($expected, UrlEncoder::unescapeAndEncode($input));
    }

    public function unescapeAndEncodeTestProvider()
    {
        return array(
            array('(foo)', '(foo)'),
            array('/my uri', '/my%20uri'),
            array('`', '%60'),
            array('~', '~'),
            array('!', '!'),
            array('@', '@'),
            array('#', '#'),
            array('$', '$'),
            array('%', '%25'),
            array('^', '%5E'),
            array('&', '&'),
            array('*', '*'),
            array('(', '('),
            array(')', ')'),
            array('-', '-'),
            array('_', '_'),
            array('=', '='),
            array('+', '+'),
            array('{', '%7B'),
            array('}', '%7D'),
            array('[', '%5B'),
            array(']', '%5D'),
            array('\\', '%5C'),
            array('|', '%7C'),
            array(';', ';'),
            array('\'', '\''),
            array(':', ':'),
            array('"', '%22'),
            array(',', ','),
            array('.', '.'),
            array('/', '/'),
            array('<', '%3C'),
            array('>', '%3E'),
            array('?', '?'),
        );
    }
}
