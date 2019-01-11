<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
require_once __DIR__.'/../../../../lib/Twig/Extensions/Extension/Array.php';

class Twig_Tests_Extension_ArrayTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider getMergeRecursiveTestData
     */
    public function testMergeRecursive($array1, $array2, $expectedArray)
    {
        $this->assertSame($expectedArray, twig_array_merge_recursive($array1, $array2));
    }

    public function getMergeRecursiveTestData()
    {
        return array(
            array(
                array('foo'),
                array('bar'),
                array(
                    'foo',
                    'bar',
                ),
            ),
            array(
                array('foo' => 'bar'),
                array('foo' => 'baz'),
                array('foo' => array('bar', 'baz')),
            ),
            array(
                array('foo' => 'bar'),
                array('foo' => array('baz', 'qux')),
                array('foo' => array('bar', 'baz', 'qux')),
            ),
            array(
                array('foo' => array('bar', 'baz')),
                array('foo' => array('qux', 'quux')),
                array('foo' => array('bar', 'baz', 'qux', 'quux')),
            ),
            array(
                array('foo' => 'bar'),
                array('baz' => 'qux'),
                array('foo' => 'bar', 'baz' => 'qux'),
            ),
            array(
                array('foo' => 'bar', 'baz'),
                array('qux' => 'quux', 'quuz'),
                array('foo' => 'bar', 'baz', 'qux' => 'quux', 'quuz'),
            ),
            array(
                new ArrayObject(array('foo' => 'bar', 'baz')),
                new ArrayObject(array('qux' => 'quux', 'quuz')),
                array('foo' => 'bar', 'baz', 'qux' => 'quux', 'quuz'),
            ),
            array(
                new ArrayObject(array('foo' => new ArrayObject(array('bar', 'baz')))),
                array('foo' => new ArrayObject(array('qux', 'quux'))),
                array('foo' => array('bar', 'baz', 'qux', 'quux')),
            ),
        );
    }

    /**
     * @dataProvider getShuffleFilterTestData
     */
    public function testShuffleFilter($data, $expectedElements)
    {
        foreach ($expectedElements as $element) {
            $this->assertTrue(in_array($element, twig_shuffle_filter($data), true)); // assertContains() would not consider the type
        }
    }

    public function testShuffleFilterOnEmptyArray()
    {
        $this->assertEquals(array(), twig_shuffle_filter(array()));
    }

    public function getShuffleFilterTestData()
    {
        return array(
            array(
                array(1, 2, 3),
                array(1, 2, 3),
            ),
            array(
                array('a' => 'apple', 'b' => 'orange', 'c' => 'citrus'),
                array('apple', 'orange', 'citrus'),
            ),
            array(
                new ArrayObject(array('apple', 'orange', 'citrus')),
                array('apple', 'orange', 'citrus'),
            ),
        );
    }
}
