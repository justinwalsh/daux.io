<?php

namespace League\CommonMark\Tests\Unit\Util;

use League\CommonMark\Util\Html5Entities;

class Html5EntitiesTest extends \PHPUnit_Framework_TestCase
{
    public function testEntityToChar()
    {
        $this->assertEquals('©', Html5Entities::decodeEntity('&copy;'));
        $this->assertEquals('&copy', Html5Entities::decodeEntity('&copy'));
        $this->assertEquals('&MadeUpEntity;', Html5Entities::decodeEntity('&MadeUpEntity;'));
        $this->assertEquals('#', Html5Entities::decodeEntity('&#35;'));
        $this->assertEquals('Æ', Html5Entities::decodeEntity('&AElig;'));
        $this->assertEquals('Ď', Html5Entities::decodeEntity('&Dcaron;'));
    }
}
