<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Util;

use League\CommonMark\Util\ArrayCollection;

class ArrayCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorAndToArray()
    {
        $collection = new ArrayCollection();
        $this->assertEquals(array(), $collection->toArray());

        $array = array();
        $collection = new ArrayCollection($array);
        $this->assertEquals($array, $collection->toArray());

        $array = array('foo' => 'bar');
        $collection = new ArrayCollection($array);
        $this->assertEquals($array, $collection->toArray());
    }

    public function testFirst()
    {
        $collection = new ArrayCollection(array('foo', 'bar'));
        $this->assertEquals('foo', $collection->first());
    }

    public function testLast()
    {
        $collection = new ArrayCollection(array('foo', 'bar'));
        $this->assertEquals('bar', $collection->last());
    }

    public function testGetIterator()
    {
        $array = array('foo' => 'bar');
        $collection = new ArrayCollection($array);
        $iterator = $collection->getIterator();

        $this->assertTrue($iterator instanceof \ArrayIterator);
        $this->assertEquals($array, $iterator->getArrayCopy());
    }

    public function testAdd()
    {
        $collection = new ArrayCollection();
        $collection->add('foo');

        $this->assertEquals(array('foo'), $collection->toArray());

        $collection->add('bar');

        $this->assertEquals(array('foo', 'bar'), $collection->toArray());
    }

    public function testSet()
    {
        $collection = new ArrayCollection(array('foo'));
        $collection->set('foo', 1);

        $this->assertEquals(array('foo', 'foo' => 1), $collection->toArray());

        $collection->set('foo', 2);

        $this->assertEquals(array('foo', 'foo' => 2), $collection->toArray());
    }

    public function testGet()
    {
        $collection = new ArrayCollection(array('foo' => 1, 'bar'));

        $this->assertEquals(1, $collection->get('foo'));
        $this->assertEquals('bar', $collection->get(0));
        $this->assertNull($collection->get('bar'));
    }

    public function testRemove()
    {
        $collection = new ArrayCollection(array('foo' => 1, 'bar', 'baz'));

        $removed = $collection->remove('foo');
        $this->assertEquals(1, $removed);
        $this->assertEquals(array('bar', 'baz'), $collection->toArray());

        $removed = $collection->remove('foo');
        $this->assertNull($removed);
        $this->assertEquals(array('bar', 'baz'), $collection->toArray());

        $removed = $collection->remove(0);
        $this->assertEquals('bar', $removed);
        $this->assertEquals(array(1 => 'baz'), $collection->toArray());

        $removed = $collection->remove(1);
        $this->assertEquals('baz', $removed);
        $this->assertEquals(array(), $collection->toArray());
    }

    public function testIsEmpty()
    {
        $collection = new ArrayCollection();
        $this->assertTrue($collection->isEmpty());

        $collection = new ArrayCollection(array());
        $this->assertTrue($collection->isEmpty());

        $collection = new ArrayCollection(array('foo'));
        $this->assertFalse($collection->isEmpty());

        $collection = new ArrayCollection();
        $collection->add('foo');
        $this->assertFalse($collection->isEmpty());
    }

    public function testContains()
    {
        $object = new \stdClass();
        $number = 3;
        $string = 'foo';

        $collection = new ArrayCollection(array($object, $number, $string));

        $this->assertTrue($collection->contains($object));
        $this->assertFalse($collection->contains(new \stdClass()));

        $this->assertTrue($collection->contains($number));
        $this->assertTrue($collection->contains(3));
        $this->assertFalse($collection->contains(3.000));

        $this->assertTrue($collection->contains($string));
        $this->assertTrue($collection->contains('foo'));
        $this->assertFalse($collection->contains('FOO'));
    }

    public function testIndexOf()
    {
        $object = new \stdClass();
        $number = 3;
        $string = 'foo';

        $collection = new ArrayCollection(array($object, $number, $string));

        $this->assertTrue(0 === $collection->indexOf($object));
        $this->assertTrue(false === $collection->indexOf(new \stdClass()));

        $this->assertTrue(1 === $collection->indexOf($number));
        $this->assertTrue(1 === $collection->indexOf(3));
        $this->assertTrue(false === $collection->indexOf(3.000));

        $this->assertTrue(2 === $collection->indexOf($string));
        $this->assertTrue(2 === $collection->indexOf('foo'));
        $this->assertTrue(false === $collection->indexOf('FOO'));
    }

    public function testContainsKey()
    {
        $collection = new ArrayCollection(array('foo' => 1, 'bar'));

        $this->assertTrue($collection->containsKey('foo'));
        $this->assertTrue($collection->containsKey(0));

        $this->assertFalse($collection->containsKey('FOO'));
        $this->assertFalse($collection->containsKey(1));
    }

    public function testCount()
    {
        $collection = new ArrayCollection();
        $this->assertEquals(0, $collection->count());

        $collection = new ArrayCollection(array());
        $this->assertEquals(0, $collection->count());

        $collection = new ArrayCollection(array('foo'));
        $this->assertEquals(1, $collection->count());

        $collection->add('bar');
        $this->assertEquals(2, $collection->count());

        $collection->remove(0);
        $this->assertEquals(1, $collection->count());
    }

    public function testOffsetExists()
    {
        $collection = new ArrayCollection(array('foo' => 1, 'bar'));

        $this->assertTrue($collection->offsetExists('foo'));
        $this->assertTrue($collection->offsetExists(0));

        $this->assertFalse($collection->offsetExists('FOO'));
        $this->assertFalse($collection->offsetExists(1));
    }

    public function testOffsetGet()
    {
        $collection = new ArrayCollection(array('foo' => 1, 'bar'));

        $this->assertEquals(1, $collection->offsetGet('foo'));
        $this->assertEquals('bar', $collection->offsetGet(0));
        $this->assertNull($collection->offsetGet('bar'));
    }

    public function testOffsetSet()
    {
        $collection = new ArrayCollection();
        $collection->offsetSet(null, 'foo');

        $this->assertEquals(array('foo'), $collection->toArray());

        $collection->offsetSet(null, 'bar');

        $this->assertEquals(array('foo', 'bar'), $collection->toArray());

        $collection = new ArrayCollection(array('foo'));
        $collection->offsetSet('foo', 1);

        $this->assertEquals(array('foo', 'foo' => 1), $collection->toArray());

        $collection->offsetSet('foo', 2);

        $this->assertEquals(array('foo', 'foo' => 2), $collection->toArray());
    }

    public function testOffsetUnset()
    {
        $collection = new ArrayCollection(array('foo' => 1, 'bar', 'baz'));

        $removed = $collection->offsetUnset('foo');
        $this->assertNull($removed);
        $this->assertEquals(array('bar', 'baz'), $collection->toArray());

        $removed = $collection->offsetUnset('foo');
        $this->assertNull($removed);
        $this->assertEquals(array('bar', 'baz'), $collection->toArray());

        $removed = $collection->offsetUnset(0);
        $this->assertNull($removed);
        $this->assertEquals(array(1 => 'baz'), $collection->toArray());

        $removed = $collection->offsetUnset(1);
        $this->assertNull($removed);
        $this->assertEquals(array(), $collection->toArray());
    }

    public function testSlice()
    {
        $collection = new ArrayCollection(array('foo' => 1, 0 => 'bar', 1 => 'baz', 2 => 2));

        $this->assertEquals(array('foo' => 1, 0 => 'bar', 1 => 'baz', 2 => 2), $collection->slice(0));
        $this->assertEquals(array('foo' => 1, 0 => 'bar', 1 => 'baz', 2 => 2), $collection->slice(0, null));
        $this->assertEquals(array('foo' => 1, 0 => 'bar', 1 => 'baz', 2 => 2), $collection->slice(0, 99));
        $this->assertEquals(array('foo' => 1, 0 => 'bar', 1 => 'baz', 2 => 2), $collection->slice(0, 4));
        $this->assertEquals(array('foo' => 1, 0 => 'bar', 1 => 'baz'), $collection->slice(0, 3));
        $this->assertEquals(array('foo' => 1, 0 => 'bar'), $collection->slice(0, 2));
        $this->assertEquals(array('foo' => 1), $collection->slice(0, 1));
        $this->assertEquals(array(), $collection->slice(0, 0));

        $this->assertEquals(array(0 => 'bar', 1=> 'baz', 2 => 2), $collection->slice(1));
        $this->assertEquals(array(0 => 'bar', 1=> 'baz', 2 => 2), $collection->slice(1, null));
        $this->assertEquals(array(0 => 'bar', 1=> 'baz', 2 => 2), $collection->slice(1, 99));
        $this->assertEquals(array(0 => 'bar', 1=> 'baz', 2 => 2), $collection->slice(1, 3));
        $this->assertEquals(array(0 => 'bar', 1=> 'baz'), $collection->slice(1, 2));
        $this->assertEquals(array(0 => 'bar'), $collection->slice(1, 1));
        $this->assertEquals(array(), $collection->slice(1, 0));

        $this->assertEquals(array(1=> 'baz', 2 => 2), $collection->slice(2));
        $this->assertEquals(array(1=> 'baz', 2 => 2), $collection->slice(2, null));
        $this->assertEquals(array(1=> 'baz', 2 => 2), $collection->slice(2, 99));
        $this->assertEquals(array(1=> 'baz', 2 => 2), $collection->slice(2, 2));
        $this->assertEquals(array(1 => 'baz'), $collection->slice(2, 1));
        $this->assertEquals(array(), $collection->slice(2, 0));

        $this->assertEquals(array(2 => 2), $collection->slice(3));
        $this->assertEquals(array(2 => 2), $collection->slice(3, null));
        $this->assertEquals(array(2 => 2), $collection->slice(3, 99));
        $this->assertEquals(array(2 => 2), $collection->slice(3, 1));
        $this->assertEquals(array(), $collection->slice(3, 0));

        $this->assertEquals(array(), $collection->slice(4));
        $this->assertEquals(array(), $collection->slice(99));
        $this->assertEquals(array(), $collection->slice(99, 99));
    }

    public function testToArray()
    {
        $collection = new ArrayCollection();
        $this->assertEquals(array(), $collection->toArray());

        $collection = new ArrayCollection(array());
        $this->assertEquals(array(), $collection->toArray());

        $collection = new ArrayCollection(array(1));
        $this->assertEquals(array(1), $collection->toArray());

        $collection = new ArrayCollection(array(2 => 1, 'foo'));
        $this->assertEquals(array(2 => 1, 'foo'), $collection->toArray());
    }

    public function testReplaceWith()
    {
        $collection = new ArrayCollection(array('foo' => 1, 'bar'));
        $replaced = $collection->replaceWith(array('baz', 42));

        $this->assertEquals($collection, $replaced);
        $this->assertEquals(array('baz', 42), $collection->toArray());
        $this->assertEquals(array('baz', 42), $replaced->toArray());
    }

    public function testRemoveGaps()
    {
        $collection = new ArrayCollection(array('', true, false, null, array(), 0, '0', 1));

        $collection->removeGaps();
        $this->assertEquals(array(1 => true, 7 => 1), $collection->toArray());
    }
}
