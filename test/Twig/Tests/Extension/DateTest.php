<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
require_once __DIR__ . '/../../../../lib/Twig/Extensions/Extension/Date.php';

class Twig_Tests_Extension_DateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getAgeFilterTestData
     */
    public function testAgeFilter($data, $expected)
    {
        $this->assertEquals($expected, twig_age_filter($data));
    }


    public function getAgeFilterTestData()
    {
        return array(
            array(
                new \DateTime(),
                0,
            ),
            array(
                new \DateTime('1980-01-17'),
                34,
            ),
            array(
                '17.01.1980',
                34,
            ),
            array(
                '1980-01-17',
                34,
            ),
        );
    }
}
