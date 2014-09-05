<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
require_once __DIR__ . '/../../../../lib/Twig/Extensions/Extension/Array.php';

class Twig_Tests_Extension_ArrayTest extends PHPUnit_Framework_TestCase
{
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
                array(1, 2, 3)
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

    /**
     * @dataProvider getSumFilterTestData
     */
    public function testSumFilter($expectedSum, $array)
    {
        $this->assertEquals($expectedSum, twig_sum_filter($array));
    }

    public function getSumFilterTestData()
    {
        return array(
            array(6,    array(1, 2, 3)),
            array(3.14, array(0, 1, 0.5, 0.5, 1.14)),
            array(6,    array("a" => 1, "b" => 2, 3)),
            array(3.14, array("a" => 0, 1, "b" => 0.5, 0.5, "c" => 1.14)),
            array(3.14, array(true, "1", "1.14")),
            array(8,    array("a", 1, "b", 2, "c", 3, null, false, true, "1")),
            array(0,    array())
        );
    }
}
