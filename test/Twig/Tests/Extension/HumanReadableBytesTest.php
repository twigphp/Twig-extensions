<?php namespace AppBundle\Tests\Twig\Extension;

use AppBundle\Twig\Extension\HumanReadableBytes;

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
        // 1,024 = KB (*technically* KiB)
        // 1,048,576 = MB (*technically* MiB)
        // 1,073,741,824 = GB (*technically* GiB)
        // 1,099,511,627,776 = TB (*technically* TiB)

        return [
            'data'             => [
                4868277,
                21864948,
                88667605,
                113789253,
                116658272,
                713,
                1153,
                12673,
                17856,
                19391
            ],
            'parameters'       => [
                [
                    'decimal_places'      => null,
                    'decimal_point'       => null,
                    'thousands_separator' => null,
                ],
                [
                    'decimal_places'      => 4,
                    'decimal_point'       => ',',
                    'thousands_separator' => ' ',
                ],
                [
                    'decimal_places'      => 3,
                    'decimal_point'       => '!',
                    'thousands_separator' => '|',
                ],
                [
                    'decimal_places'      => 2,
                    'decimal_point'       => ',',
                    'thousands_separator' => '.',
                ],
                [
                    'decimal_places'      => 8,
                    'decimal_point'       => '_',
                    'thousands_separator' => '*',
                ],
                [
                    'decimal_places'      => '',
                    'decimal_point'       => '',
                    'thousands_separator' => '',
                ],
                [
                    'decimal_places'      => 3,
                    'decimal_point'       => ' ',
                    'thousands_separator' => '',
                ],
                [
                    'decimal_places'      => 6,
                    'decimal_point'       => null,
                    'thousands_separator' => null,
                ],
            ],
            'expected_results' => [
                '5 MB',
                '20,8520 MB',
                '84!560 MB',
                '10,60 GB',
                '2*417_68506730 TB',
                '713 B',
                '1 123 KB',
                '12.375977 KB',
            ],
        ];
    }

    /**
     * @param array|null $data
     */
    public function testHumanReadableBytes(array $data = null)
    {
        if (empty($data)) {
            $data = $this->getHumanReadableBytesTestData();
        }

        $dataCount = count($data);

        for ($i = 0; $i < $dataCount; $i++) {
            $test = $this->humanReadableBytesExtension->humanReadableBytesFilter($data['data'][$i],
                $data['parameters'][$i]['decimal_places'],
                $data['parameters'][$i]['decimal_point'],
                $data['parameters'][$i]['thousands_separator']);

            $this->assertEquals($test, $data['expected_results'][$i]);
        }
    }
}
