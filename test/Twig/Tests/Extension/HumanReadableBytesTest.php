<?php

class Twig_Extensions_Extension_HumanReadableBytesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Twig_Extensions_Extension_HumanReadableBytesTest
     */
    protected $humanReadableBytesExtension;

    /**
     * @var array
     */
    protected $humanReadableBytesTestData;

    /**
     * @var array
     */
    protected $humanReadableBytesFormatTestData;

    /**
     * @var array
     */
    protected $humanReadableBytesDefaultExpectedResults;

    /**
     * @var array
     */
    protected $humanReadableBytesFormattedExpectedResults;

    /**
     * Sets test data.
     */
    protected function setTestData()
    {
        $this->humanReadableBytesTestData = array(
            486827,
            21864948,
            11378925384,
            4121781686582782,
            713,
        );

        $this->humanReadableBytesFormatTestData = array(
            array(
                'bytes' => 486827,
                'decimalPlaces' => 3,
                'decimalPoint' => ',',
                'thousandsSeparator' => ' ',
                'units' => 'SI',
            ),
            array(
                'bytes' => 21864948,
                'decimalPlaces' => null,
                'decimalPoint' => null,
                'thousandsSeparator' => null,
                'units' => null,
            ),
            array(
                'bytes' => 11378925384,
                'decimalPlaces' => 8,
                'decimalPoint' => ' ',
                'thousandsSeparator' => '.',
                'units' => 'SI',
            ),
            array(
                'bytes' => 4121781686582782,
                'decimalPlaces' => 4,
                'decimalPoint' => '-',
                'thousandsSeparator' => '_',
                'units' => 'SI',
            ),
            array(
                'bytes' => 713,
                'decimalPlaces' => 0,
                'decimalPoint' => '.',
                'thousandsSeparator' => ',',
                'units' => 'IEC',
            ),
        );
    }

    protected function setExpectedResults()
    {
        // defaults are:
        //  2 decimal places,
        //  "." decimal point,
        //  "," thousands separator
        //  IEC units

        $this->humanReadableBytesDefaultExpectedResults = array(
            '475.42 KiB',
            '20.85 MiB',
            '10.60 GiB',
            '3,748.74 TiB',
            '713.00 B',
        );

        $this->humanReadableBytesFormattedExpectedResults = array(
            '486,827 KB',
            '21 MiB',
            '11 37892538 GB',
            '4_121-7817 TB',
            '713 B',
        );
    }

    public function setUp()
    {
        $this->humanReadableBytesExtension = new Twig_Extensions_Extension_HumanReadableBytes();
        $this->setTestData();
        $this->setExpectedResults();
    }


    /**
     * Tests defaults.
     */
    public function testHumanReadableBytesConversion()
    {
        foreach ($this->humanReadableBytesTestData as $key => $value) {
            $test = $this->humanReadableBytesExtension->humanReadableBytesFilter($value);
            $this->assertEquals($test, $this->humanReadableBytesDefaultExpectedResults[$key]);
        }
    }

    /**
     * Run test; assert output is properly formatted.
     */
    public function testHumanReadableBytesFormatting()
    {
        foreach ($this->humanReadableBytesFormatTestData as $key => $value) {
            $test = $this->humanReadableBytesExtension->humanReadableBytesFilter(
                $value['bytes'],
                $value['decimalPlaces'],
                $value['decimalPoint'],
                $value['thousandsSeparator'],
                $value['units']
            );
            $this->assertEquals($test, $this->humanReadableBytesFormattedExpectedResults[$key]);
        }
    }

}
