<?php

use \Twig_Error;
use \Twig_Extension;
use \Twig_SimpleFilter;

class Twig_Extensions_Extension_HumanReadableBytes extends \Twig_Extension
{
    private $KILOBYTE = 1000;
    private $MEGABYTE = self::KILOBYTE * 1000;
    private $GIGABYTE = self::MEGABYTE * 1000;
    private $TERABYTE = self::GIGABYTE * 1000;
    
    private $KIBIBYTE = 1024;
    private $MEBIBYTE = self::KIBIBYTE * 1024;
    private $GIBIBYTE = self::MEBIBYTE * 1024;
    private $TEBIBYTE = self::GIBIBYTE * 1024;
    
    private $SUFFIX_SI = 'B';
    private $SUFFIX_IEC = 'iB';
    
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter('readBytes', array(
                $this,
                'humanReadableBytesFilter',
            )),
        );
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

        switch ($format) {
            case 'SI':
                $multipliers = array(
                    'K' => self::KILOBYTE,
                    'M' => self::MEGABYTE,
                    'G' => self::GIGABYTE,
                    'T' => self::TERABYTE,
                );

                $suffix = self::SUFFIX_SI;
                break;
            case 'IEC':
            default:
                $multipliers = array(
                    'K' => self::KIBIBYTE,
                    'M' => self::MEBIBYTE,
                    'G' => self::GIBIBYTE,
                    'T' => self::TEBIBYTE,
                );

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
