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
 * Filesize extension for human readable filesizes.
 *
 * @author Patrik Karisch <p.karisch@pixelart.at>
 */
class Twig_Extensions_Extension_Filesize extends \Twig_Extension
{
    private static $two = array('Byte', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB');
    private static $ten = array('Byte', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB');

    /**
     * The default suffix.
     *
     * @var string|null
     */
    private $fixedSuffix;

    /**
     * Use power of 2 or 10 if not given in the filter.
     *
     * @var bool
     */
    private $powerOfTwo;

    /**
     * @param string $fixedSuffix Use this fixed suffix to calculate. Must be a correct
     *                            filesize suffix and corresponding to $powerOfTwo.
     * @param bool   $powerOfTwo  Whether to use powers of 2 or 10.
     */
    public function __construct($fixedSuffix = null, $powerOfTwo = true)
    {
        $this->fixedSuffix = $fixedSuffix;
        $this->powerOfTwo = $powerOfTwo;
    }

    /**
     * @return string|null The default suffix to use when calculating or none.
     */
    public function getFixedSuffix()
    {
        return $this->fixedSuffix;
    }

    /**
     * @param string|null $fixedSuffix Use this fixed suffix to calculate. Must be a correct
     *                                 filesize suffix and corresponding to $powerOfTwo or null.
     */
    public function setFixedSuffix($fixedSuffix)
    {
        $this->fixedSuffix = $fixedSuffix;
    }

    /**
     * @return bool Use power of 2 or 10 if not given in the filter.
     */
    public function isPowerOfTwo()
    {
        return $this->powerOfTwo;
    }

    /**
     * @param bool $powerOfTwo Whether to use powers of 2 or 10.
     */
    public function setPowerOfTwo($powerOfTwo)
    {
        $this->powerOfTwo = $powerOfTwo;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('filesize', array($this, 'filesize'), array(
                'needs_environment' => true,
            )),
        );
    }

    /**
     * Calculates a human readable file size from a file size in bytes.
     *
     * @param Twig_Environment $env          A Twig_Environment instance.
     * @param mixed            $size         A float/int/string of the size to format.
     * @param string           $fixedSuffix  Use this fixed suffix to calculate. Must be a correct
     *                                       filesize suffix and corresponding to $powerOfTwo.
     * @param bool             $powerOfTwo   Whether to use powers of 2 or 10.
     * @param int              $decimal      The number of decimal points to display.
     * @param string           $decimalPoint The character(s) to use for the decimal point.
     * @param string           $thousandSep  The character(s) to use for the thousands separator.
     *
     * @return string The file size with prefix.
     */
    public function filesize(Twig_Environment $env, $size, $fixedSuffix = null, $powerOfTwo = null, $decimal = null, $decimalPoint = null, $thousandSep = null) {
        if (null === $fixedSuffix) {
            $fixedSuffix = $this->fixedSuffix;
        }
        if (null === $powerOfTwo) {
            $powerOfTwo = $this->powerOfTwo;
        }
        $numberDefaults = $env->getExtension('core')->getNumberFormat();
        if (null === $decimal) {
            $decimal = $numberDefaults[0];
        }
        if (null === $decimalPoint) {
            $decimalPoint = $numberDefaults[1];
        }
        if (null === $thousandSep) {
            $thousandSep = $numberDefaults[2];
        }

        $divide = $powerOfTwo ? 1024 : 1000;
        $suffix = $powerOfTwo ? self::$two : self::$ten;

        $cycles = count($suffix);
        if ($fixedSuffix !== null && false !== $suffixKey = array_search(
                $fixedSuffix,
                $suffix,
                true
            )
        ) {
            $size /= pow($divide, $suffixKey);
        } else {
            for ($suffixKey = 0; $size > $divide && $suffixKey < $cycles; ++$suffixKey) {
                $size /= $divide;
            }

            if ($suffixKey >= $cycles) {
                --$suffixKey;
            }
        }

        $size = round($size, $decimal);
        $size = number_format($size, $decimal, $decimalPoint, $thousandSep);

        return $size.' '.$suffix[$suffixKey];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'filesize';
    }
}
