<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (http://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Extension;

class ExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultMethodBehavior()
    {
        $extension = $this->getMockForAbstractClass('League\CommonMark\Extension\Extension');
        $this->assertEquals(array(), $extension->getBlockParsers());
        $this->assertEquals(array(), $extension->getBlockRenderers());
        $this->assertEquals(array(), $extension->getInlineParsers());
        $this->assertEquals(array(), $extension->getInlineProcessors());
        $this->assertEquals(array(), $extension->getInlineRenderers());
    }
}
