<?php

use \Twig_Error as TwigError;
use \Twig_Extension;
use \Twig_SimpleFilter;

class Twig_Extensions_Extension_HumanReadableBytes extends \Twig_Extension
{
    private $KILOBYTE;
    private $MEGABYTE;
    private $GIGABYTE;
    private $TERABYTE;

    private $KIBIBYTE;
    private $MEBIBYTE;
    private $GIBIBYTE;
    private $TEBIBYTE;

    private $SUFFIX_SI;
    private $SUFFIX_IEC;

    private function setUnitValues()
    {
        $this->KILOBYTE = 1000;
        $this->MEGABYTE = $this->KILOBYTE * 1000;
        $this->GIGABYTE = $this->MEGABYTE * 1000;
        $this->TERABYTE = $this->GIGABYTE * 1000;
     
        $this->KIBIBYTE = 1024;
        $this->MEBIBYTE = $this->KIBIBYTE * 1024;
        $this->GIBIBYTE = $this->MEBIBYTE * 1024;
        $this->TEBIBYTE = $this->GIBIBYTE * 1024;

        $this->SUFFIX_SI = 'B';
        $this->SUFFIX_IEC = 'iB';
    }
    
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('readBytes', array(
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
        $this->setUnitValues();
        
        if (!is_numeric($bytes)) {
            throw new TwigError('Data must be numeric');
        }

        switch ($format) {
            case 'SI':
                $multipliers = array(
                    'K' => $this->KILOBYTE,
                    'M' => $this->MEGABYTE,
                    'G' => $this->GIGABYTE,
                    'T' => $this->TERABYTE,
                );

                $suffix = $this->SUFFIX_SI;
                break;
            case 'IEC':
            default:
                $multipliers = array(
                    'K' => $this->KIBIBYTE,
                    'M' => $this->MEBIBYTE,
                    'G' => $this->GIBIBYTE,
                    'T' => $this->TEBIBYTE,
                );

                $suffix = $this->SUFFIX_IEC;
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
