<?php

require_once __DIR__.'../../../../lib/Twig/Extensions/Extension/HumanReadableBytes.php';

class HumanReadableBytesTest extends \PHPUnit_Framework_TestCase
{
    protected $humanReadableBytesExtension;

    public function __construct()
    {
        parent::__construct();

        $this->humanReadableBytesExtension = new HumanReadableBytes();
    }

    public function getHumanReadableBytesTestData()
    {
        return [
            'data' => [
                4868277,
                21864948,
                88667605,
                11378925384,
                4121781686582782,
                713,
                1153,
                12673,
            ],
            'parameters' => [
                [
                    'decimal_places' => null,
                    'decimal_point' => null,
                    'thousands_separator' => null,
                    'format' => null,
                ],
                [
                    'decimal_places' => 4,
                    'decimal_point' => ',',
                    'thousands_separator' => ' ',
                    'format' => '',
                ],
                [
                    'decimal_places' => 3,
                    'decimal_point' => '!',
                    'thousands_separator' => '|',
                    'format' => 'SI',
                ],
                [
                    'decimal_places' => 2,
                    'decimal_point' => ',',
                    'thousands_separator' => '.',
                    'format' => 'IEC',
                ],
                [
                    'decimal_places' => 8,
                    'decimal_point' => '_',
                    'thousands_separator' => '*',
                    'format' => 'SI',
                ],
                [
                    'decimal_places' => 0,
                    'decimal_point' => '',
                    'thousands_separator' => '',
                    'format' => 'SI',
                ],
                [
                    'decimal_places' => 3,
                    'decimal_point' => ' ',
                    'thousands_separator' => '',
                    'format' => 'SI',
                ],
                [
                    'decimal_places' => 6,
                    'decimal_point' => null,
                    'thousands_separator' => null,
                    'format' => 'SI',
                ],
            ],
            'expected_results' => [
                '5 MiB',
                '20,8520 MiB',
                '88!668 MB',
                '10,60 GiB',
                '4*121_78168658 TB',
                '713 B',
                '1 153 KB',
                '12.673000 KB',
            ],
        ];
    }

    /**
     * Run test; assert output is properly formatted.
     */
    public function testHumanReadableBytes()
    {
        $data = $this->getHumanReadableBytesTestData();

        $dataCount = count($data['data']);

        for ($i = 0; $i < $dataCount; ++$i) {
            $test = $this->humanReadableBytesExtension->humanReadableBytesFilter(
                $data['data'][$i],
                $data['parameters'][$i]['decimal_places'],
                $data['parameters'][$i]['decimal_point'],
                $data['parameters'][$i]['thousands_separator'],
                $data['parameters'][$i]['format']
            );

            $this->assertEquals($test, $data['expected_results'][$i]);
        }
    }
}
