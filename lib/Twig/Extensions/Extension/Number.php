<?php

/**
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This code has been taken from BrasilianFOS Extension Bundle
 *
 * @author Emanuele Panzeri <thepanz@gmail.com>
 * @author Paulo Ribeiro <paulo@duocriativa.com.br>
 * @package Twig
 * @subpackage Twig-extensions
 */
class Twig_Extensions_Extension_Number extends Twig_Extension
{
    /**
     * Returns a list of filters.
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
           new Twig_SimpleFilter('format_bytes', array($this, 'format_bytes')),
        );
    }

    /**
     * Name of this extension
     *
     * @return string
     */
    public function getName()
    {
        return 'Number';
    }

    /**
     * Filter for converting bytes to a human-readable format, as Unix command "ls -h" does.
     *
     * @param string|int     $bytes A string or integer number value to format.
     * @param bool    $base2conversion Defines if the conversion has to be strictly performed as binary values or
     *      by using a decimal conversion such as 1 KByte = 1000 Bytes.
     *
     * @return string The number converted to human readable representation.
     * @todo: Use Intl-based translations to deal with "11.4" conversion to "11,4" value
     */
    public function format_bytes($bytes, $base2conversion = true)
    {
        $unit = $base2conversion ? 1024 : 1000;
        if ($bytes < $unit) {
            return $bytes . " B";
        }
        $exp = intval((log($bytes) / log($unit)));
        $pre = ($base2conversion ? "kMGTPE" : "KMGTPE");
        $pre = $pre[$exp - 1] . ($base2conversion ? "" : "i");

        return sprintf("%.1f %sB", $bytes / pow($unit, $exp), $pre);
    }

}
