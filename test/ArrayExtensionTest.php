<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Twig\Tests\Extension;

use PHPUnit\Framework\TestCase;
use Twig\Extensions\ArrayExtension;

class_exists(ArrayExtension::class);

class ArrayExtensionTest extends TestCase
{
    /**
     * @dataProvider getShuffleFilterTestData
     */
    public function testShuffleFilter($data, $expectedElements)
    {
        foreach ($expectedElements as $element) {
            $this->assertTrue(\in_array($element, \twig_shuffle_filter($data), true)); // assertContains() would not consider the type
        }
    }

    public function testShuffleFilterOnEmptyArray()
    {
        $this->assertEquals([], \twig_shuffle_filter([]));
    }

    public function getShuffleFilterTestData()
    {
        return [
            [
                [1, 2, 3],
                [1, 2, 3],
            ],
            [
                ['a' => 'apple', 'b' => 'orange', 'c' => 'citrus'],
                ['apple', 'orange', 'citrus'],
            ],
            [
                new \ArrayObject(['apple', 'orange', 'citrus']),
                ['apple', 'orange', 'citrus'],
            ],
        ];
    }
}
