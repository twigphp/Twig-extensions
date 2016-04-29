<?php

class HumanReadableBytes extends \Twig_Extension
{
    const KILOBYTE = 1024;
    const MEGABYTE = 1024 * self::KILOBYTE;
    const GIGABYTE = 1024 * self::MEGABYTE;
    const TERABYTE = 1024 * self::GIGABYTE;

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('readBytes', [
                $this,
                'humanReadableBytesFilter'
            ]),
        ];
    }

    /**
     * Return a human-readable string from numeric bytes input
     *
     * @param        $bytes
     * @param int    $decimalPlaces
     * @param string $decimalPoint
     * @param string $thousandsSeparator
     *
     * @return string
     * @throws \Twig_Error
     */
    public function humanReadableBytesFilter($bytes, $decimalPlaces = 2, $decimalPoint = '.', $thousandsSeparator = ',')
    {
        if (!is_numeric($bytes)) {
            throw new \Twig_Error('Data must be numeric');
        }

        if ($bytes < self::KILOBYTE) {
            $readable = number_format($bytes, $decimalPlaces, $decimalPoint, $thousandsSeparator) . ' B';
        } elseif ($bytes < self::MEGABYTE) {
            $readable = number_format($bytes / self::KILOBYTE, $decimalPlaces, $decimalPoint, $thousandsSeparator) . ' KB';
        } elseif ($bytes < self::GIGABYTE) {
            $readable = number_format($bytes / self::MEGABYTE, $decimalPlaces, $decimalPoint, $thousandsSeparator) . ' MB';
        } elseif ($bytes < self::TERABYTE) {
            $readable = number_format($bytes / self::GIGABYTE, $decimalPlaces, $decimalPoint, $thousandsSeparator) . ' GB';
        } else {
            $readable = number_format($bytes / self::TERABYTE, $decimalPlaces, $decimalPoint, $thousandsSeparator) . ' TB';
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
