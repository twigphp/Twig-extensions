<?php

use Twig_Error;
use Twig_Extension;
use Twig_SimpleFilter;

class HumanReadableBytes extends Twig_Extension
{
    const KILOBYTE = 1000;
    const MEGABYTE = 1000 * self::KILOBYTE;
    const GIGABYTE = 1000 * self::MEGABYTE;
    const TERABYTE = 1000 * self::GIGABYTE;

    const KIBIBYTE = 1024;
    const MEBIBYTE = 1024 * self::KIBIBYTE;
    const GIBIBYTE = 1024 * self::MEBIBYTE;
    const TEBIBYTE = 1024 * self::GIBIBYTE;

    const SUFFIX_SI = 'B';
    const SUFFIX_IEC = 'iB';

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new Twig_SimpleFilter('readBytes', [
                $this,
                'humanReadableBytesFilter',
            ]),
        ];
    }

    /**
     * Return a human-readable string from numeric bytes input.
     *
     * @param        $bytes
     * @param int    $decimalPlaces
     * @param string $decimalPoint
     * @param string $thousandsSeparator
     *
     * @return string
     *
     * @throws \Twig_Error
     */
    public function humanReadableBytesFilter($bytes, $decimalPlaces = 2, $decimalPoint = '.', $thousandsSeparator = ',', $format = 'IEC')
    {
        if (!is_numeric($bytes)) {
            throw new Twig_Error('Data must be numeric');
        }

        $multipliers = [];

        switch ($format) {
            case 'SI':
                $multipliers = [
                    'K' => self::KILOBYTE,
                    'M' => self::MEGABYTE,
                    'G' => self::GIGABYTE,
                    'T' => self::TERABYTE,
                ];

                $suffix = self::SUFFIX_SI;
                break;
            case 'IEC':
            default:
                $multipliers = [
                    'K' => self::KIBIBYTE,
                    'M' => self::MEBIBYTE,
                    'G' => self::GIBIBYTE,
                    'T' => self::TEBIBYTE,
                ];

                $suffix = self::SUFFIX_IEC;
                break;
        }

        if ($bytes < $multipliers['K']) {
            $readable = number_format($bytes, $decimalPlaces, $decimalPoint, $thousandsSeparator).' B';
        } elseif ($bytes < $multipliers['M']) {
            $readable = number_format($bytes / $multipliers['K'], $decimalPlaces, $decimalPoint, $thousandsSeparator).' K'.$suffix;
        } elseif ($bytes < $multipliers['G']) {
            $readable = number_format($bytes / $multipliers['M'], $decimalPlaces, $decimalPoint, $thousandsSeparator).' M'.$suffix;
        } elseif ($bytes < $multipliers['T']) {
            $readable = number_format($bytes / $multipliers['G'], $decimalPlaces, $decimalPoint, $thousandsSeparator).' G'.$suffix;
        } else {
            $readable = number_format($bytes / $multipliers['T'], $decimalPlaces, $decimalPoint, $thousandsSeparator).' T'.$suffix;
        }

        return $readable;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'human_readable_bytes';
    }
}
