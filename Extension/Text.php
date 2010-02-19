<?php
/**
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Henrik Bjornskov <hb@peytz.dk>
 * @package Twig
 * @subpackage Twig-extensions
 */
class Twig_Extension_Text extends Twig_Extension
{
    /**
     * Returns a list of filters.
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
            'truncate' => new Twig_Filter_Function('twig_truncate_filter', array('needs_environment' => true)),
        );
    }
}


if (function_exists('mb_get_info')) {

    function twig_truncate_filter(Twig_Environment $env, $value, $length = 30, $separator = '...')
    {
        if (mb_strlen($value, $env->getCharset()) > $length) {
            return mb_substr($value, 0, $length, $env->getCharset()) . $separator;
        }
        
        return $value;
    }
    
} else {

    function twig_truncate_filter(Twig_Environment $env, $value, $length = 30, $separator = '...')
    {
        if (strlen($value) > $length) {
            return substr($value, 0, $length) . $separator;
        }
        
        return $value;
    }
    
}