<?php
namespace Todaymade\Daux\Format\Confluence;

class ApiTest extends \PHPUnit_Framework_TestCase
{
    // this test supports upgrade Guzzle to version 6
    public function testClientOptions()
    {
        $api = new Api('http://test.com/', 'user', 'pass');
        $this->assertEquals('test.com', $api->getClient()->getConfig()['base_uri']->getHost());
    }
}
