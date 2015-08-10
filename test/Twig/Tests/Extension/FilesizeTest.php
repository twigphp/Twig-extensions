<?php

/*
 * This file is part of Twig.
 *
 * (c) 2015 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @author Patrik Karisch <p.karisch@pixelart.at>
 */
class Twig_Tests_Extension_FilesizeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Twig_Environment
     */
    private $env;

    protected function setUp()
    {
        $coreExtension = $this->getMock('Twig_Extension_Core');
        $coreExtension
            ->expects($this->any())
            ->method('getNumberFormat')
            ->will($this->returnValue(array(0, '.', ',')));

        $this->env = $this->getMockBuilder('Twig_Environment')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->env
            ->expects($this->any())
            ->method('getExtension')
            ->with('core')
            ->will($this->returnValue($coreExtension))
        ;
    }

    /**
     * @dataProvider getFilesizeDefaultTestData()
     */
    public function testFilesizeWithDefaults($expected, $size, $powerOfTwo)
    {
        $extension = new Twig_Extensions_Extension_Filesize();
        $this->assertEquals($expected, $extension->filesize($this->env, $size, null, $powerOfTwo));
    }

    /**
     * @dataProvider getFilesizeVariableDecimalTestData()
     */
    public function testFilesizeWithVariableDecimal($expected, $decimal, $powerOfTwo)
    {
        $extension = new Twig_Extensions_Extension_Filesize();
        $this->assertEquals($expected, $extension->filesize($this->env, 752378952, null, $powerOfTwo, $decimal));
    }

    /**
     * @dataProvider getFilesizeFixedSuffixTestData()
     */
    public function testFilesizeWithFixedSuffix($expected, $size, $suffix, $powerOfTwo)
    {
        $extension = new Twig_Extensions_Extension_Filesize();
        $this->assertEquals($expected, $extension->filesize($this->env, $size, $suffix, $powerOfTwo, 2));
    }

    public function getFilesizeDefaultTestData()
    {
        return array(
            array('10 Byte', '10', true),
            array('10 KiB', '10240', true),
            array('10 MiB', '10485760', true),
            array('10 GiB', '10737418240', true),
            array('10 TiB', '10995116277760', true),
            array('10 PiB', '11258999068426240', true),
            array('5 EiB', '5764607523034234880', true),
            array('10 Byte', '10', false),
            array('10 kB', '10000', false),
            array('10 MB', '10000000', false),
            array('10 GB', '10000000000', false),
            array('10 TB', '10000000000000', false),
            array('10 PB', '10000000000000000', false),
            array('5 EB', '5000000000000000000', false),
        );
    }

    public function getFilesizeVariableDecimalTestData()
    {
        return array(
            array('718 MiB', 0, true),
            array('717.5 MiB', 1, true),
            array('717.52 MiB', 2, true),
            array('717.524 MiB', 3, true),
            array('717.5245 MiB', 4, true),
            array('717.52448 MiB', 5, true),
            array('752 MB', 0, false),
            array('752.4 MB', 1, false),
            array('752.38 MB', 2, false),
            array('752.379 MB', 3, false),
            array('752.3790 MB', 4, false),
            array('752.37895 MB', 5, false),
        );
    }

    public function getFilesizeFixedSuffixTestData()
    {
        return array(
            array('471,859.00 Byte', '471859', 'Byte', true),
            array('0.45 MiB', '471859', 'MiB', true),
            array('1,545.36 MiB', '1620427407', 'MiB', true),
            array('750,000.00 Byte', '750000', 'Byte', false),
            array('0.75 MB', '750000', 'MB', false),
            array('4,543.78 MB', '4543780000', 'MB', false),
        );
    }
}
