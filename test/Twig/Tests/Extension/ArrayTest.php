<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../../lib/Twig/Extensions/Extension/Array.php';

class Twig_Tests_Extension_ArrayTest extends TestCase
{
    /**
     * @dataProvider getShuffleFilterTestData
     * @param array|ArrayObject $data
     * @param array $expectedElements
     */
    public function testShuffleFilter($data, array $expectedElements): void
    {
        foreach ($expectedElements as $element) {
            self::assertContains(
                $element, twig_shuffle_filter($data)
            ); // assertContains() would not consider the type
        }
    }

    public function testShuffleFilterOnEmptyArray(): void
    {
        self::assertEquals([], twig_shuffle_filter([]));
    }

    /**
     * @return array
     */
    public function getShuffleFilterTestData(): array
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
                new ArrayObject(['apple', 'orange', 'citrus']),
                ['apple', 'orange', 'citrus'],
            ],
        ];
    }
}
