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

class Twig_Tests_Extension_ArrayTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getShuffleFilterTestData
     */
    public function testShuffleFilter($data, $expectedElements)
    {
        foreach ($expectedElements as $element) {
            $this->assertTrue(in_array($element, twigShuffleFilter($data), true)); // assertContains() would not consider the type
        }
    }

    public function testShuffleFilterOnEmptyArray()
    {
        $this->assertEquals(array(), twigShuffleFilter(array()));
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