<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Emanuele Panzeri <thepanz@gmail.com>
 */
require_once __DIR__.'/../../../../lib/Twig/Extensions/Extension/Number.php';

class Twig_Tests_Extension_NumberTest extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        if (!class_exists('Twig_Extensions_Extension_Number')) {
            self::markTestSkipped('Unable to find class Twig_Extensions_Extension_Number.');
        }
    }

    /**
     *
     * @dataProvider getDataBytes
     * @param $expected
     * @param $value
     * @param $base2conversion
     */
    public function testByteConversion($expected, $value, $base2conversion = true)
    {
        $extension = new Twig_Extensions_Extension_Number();
        $this->assertEquals($expected, $extension->format_bytes($value, $base2conversion));
    }

    public function getDataBytes()
    {
        return array(
            array(null, 'ThisIsAString'),
            array(null, ''),
            array(null, null),
            array('1 B', 1),
            array('1 B', 1, false),
            array('1000 B',  1000),
            array('1.0 KiB', 1000, false),
            array('1.0 kB',  1024),
            array('1.0 KiB', 1024, false),
            array('2.0 kB',  2048),
            array('2.0 KiB', 2048, false),
            array('2.0 kB',  '2048'),
            array('2.0 KiB', '2048', false),
            array('2.4 kB',  2500),
            array('2.5 KiB', 2500, false),
            array('976.6 kB', 1000000),
            array('1.0 MiB',  1000000, false),
            array('1.0 MB',   1048576),
            array('1.0 MiB',  1048576, false),
            array('953.7 MB', 1000000000),
            array('1.0 GiB',  1000000000, false),
            array('1.0 GB',   1073741824),
            array('1.1 GiB',  1073741824, false),
            array('1.0 TB',   1099511627776),
            array('1.1 TiB',  1099511627776, false),
            array('1.0 PB',   1.12589990684263e+15),
            array('1.1 PiB',  1.12589990684263e+15, false),
        );
    }
}
