<?php

/**
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Chris Sedlmayr (catchamonkey) <chris@sedlmayr.co.uk>
 * @package Twig
 * @subpackage Twig-extensions
 */
class Twig_Extensions_Extension_String extends Twig_Extension
{
    public function getFilters()
    {
        return array(
            'slugify'   => new Twig_Filter_Method($this, 'slugify'),
        );
    }

    public function slugify($string)
    {
        $ret = $string;
        // remove html line break
        $ret = preg_replace("<br/>", '', $ret);
        // strip all non word chars
        $ret = preg_replace('/\W/u', ' ', $ret);
        // replace all white space sections with a dash
        $ret = preg_replace('/\ +/', '-', $ret);
        // trim dashes
        $ret = preg_replace('/\-$/', '', $ret);
        $ret = preg_replace('/^\-/', '', $ret);
        // convert to lower case
        $ret = strtolower($ret);

        return $ret;
    }

    public function getName()
    {
        return 'string';
    }
}
