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
class Twig_Extensions_Extension_Text extends Twig_Extension
{
    /**
     * Returns a list of filters.
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter('truncate', 'twig_truncate_filter', array('needs_environment' => true)),
            new Twig_SimpleFilter('wordwrap', 'twig_wordwrap_filter', array('needs_environment' => true)),
        );
    }

    /**
     * Name of this extension
     *
     * @return string
     */
    public function getName()
    {
        return 'Text';
    }
}

if (function_exists('mb_get_info')) {
    function twig_truncate_filter(Twig_Environment $env, $input, $limit = 30, $preserve = false, $separator = '...')
    {
        $charset = $env->getCharset();
        if (mb_strlen($input, $charset) <= $limit) {
            return $input;
        }
        $cutLength = $limit - mb_strlen($separator);
        $firstCut = rtrim(mb_substr($input, 0, $cutLength, $charset));
        $isBreakpointInWord = ($input[$cutLength] !== ' ');
        if (!$preserve || !$isBreakpointInWord) {
            return $firstCut.$separator;
        }
        $lastSpace = mb_strrpos($firstCut, ' ', 0, $charset);

        return rtrim(mb_substr($firstCut, 0, $lastSpace, $charset)).$separator;
    }

    function twig_wordwrap_filter(Twig_Environment $env, $value, $length = 80, $separator = "\n", $preserve = false)
    {
        $sentences = array();

        $previous = mb_regex_encoding();
        mb_regex_encoding($env->getCharset());

        $pieces = mb_split($separator, $value);
        mb_regex_encoding($previous);

        foreach ($pieces as $piece) {
            while(!$preserve && mb_strlen($piece, $env->getCharset()) > $length) {
                $sentences[] = mb_substr($piece, 0, $length, $env->getCharset());
                $piece = mb_substr($piece, $length, 2048, $env->getCharset());
            }

            $sentences[] = $piece;
        }

        return implode($separator, $sentences);
    }
} else {
    function twig_truncate_filter(Twig_Environment $env, $input, $limit = 30, $preserve = false, $separator = '...')
    {
        $charset = $env->getCharset();
        if (strlen($input, $charset) <= $limit) {
            return $input;
        }
        $cutLength = $limit - strlen($separator);
        $firstCut = rtrim(substr($input, 0, $cutLength, $charset));
        $isBreakpointInWord = ($input[$cutLength] !== ' ');
        if (!$preserve || !$isBreakpointInWord) {
            return $firstCut.$separator;
        }
        $lastSpace = strrpos($firstCut, ' ', 0, $charset);

        return rtrim(substr($firstCut, 0, $lastSpace, $charset)).$separator;
    }

    function twig_wordwrap_filter(Twig_Environment $env, $value, $length = 80, $separator = "\n", $preserve = false)
    {
        return wordwrap($value, $length, $separator, !$preserve);
    }
}
