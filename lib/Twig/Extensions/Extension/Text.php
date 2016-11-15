<?php

/**
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @author Henrik Bjornskov <hb@peytz.dk>
 */
class Twig_Extensions_Extension_Text extends Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter('truncate', 'twig_truncate_filter', array('needs_environment' => true)),
            new Twig_SimpleFilter('wordwrap', 'twig_wordwrap_filter', array('needs_environment' => true)),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Text';
    }
}

if (function_exists('mb_get_info')) {
    function twig_truncate_filter(Twig_Environment $env, $value, $length = 30, $preserve = false, $separator = '...')
    {
        if (mb_strlen($value, $env->getCharset()) > $length) {
            if ($preserve) {
                // If breakpoint is on the last word, return the value without separator.
                if (false === ($breakpoint = mb_strpos($value, ' ', $length, $env->getCharset()))) {
                    return $value;
                }

                $length = $breakpoint;
            }

            return rtrim(mb_substr($value, 0, $length, $env->getCharset())).$separator;
        }

        return $value;
    }

    function twig_wordwrap_filter(Twig_Environment $env, $value, $length = 80, $separator = "\n", $preserve = false)
    {
        $previous = mb_regex_encoding();
        mb_regex_encoding($env->getCharset());

        $lines = mb_split($separator, $value);

        foreach ($lines as &$line) {
            $line = rtrim($line);
            if (mb_strlen($line, $env->getCharset()) <= $length) {
                continue;
            }

            $words = explode(' ', $line);
            $line = '';
            $actual = '';
            foreach ($words as $word) {
                if (mb_strlen($actual.$word, $env->getCharset()) <= $length) {
                    $actual .= $word.' ';
                } else {
                    if ('' !== $actual) {
                        $line .= rtrim($actual).$separator;
                    }
                    $actual = $word;
                    if (!$preserve) {
                        while (mb_strlen($actual, $env->getCharset()) > $length) {
                            $line .= mb_substr($actual, 0, $length).$separator;
                            $actual = mb_substr($actual, $length);
                        }
                    }
                    $actual .= ' ';
                }
            }
            $line .= trim($actual);
        }

        mb_regex_encoding($previous);

        return implode($separator, $lines);
    }
} else {
    function twig_truncate_filter(Twig_Environment $env, $value, $length = 30, $preserve = false, $separator = '...')
    {
        if (strlen($value) > $length) {
            if ($preserve) {
                if (false !== ($breakpoint = strpos($value, ' ', $length))) {
                    $length = $breakpoint;
                }
            }

            return rtrim(substr($value, 0, $length)).$separator;
        }

        return $value;
    }

    function twig_wordwrap_filter(Twig_Environment $env, $value, $length = 80, $separator = "\n", $preserve = false)
    {
        return wordwrap($value, $length, $separator, !$preserve);
    }
}
