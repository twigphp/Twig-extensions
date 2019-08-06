<?php

/**
 * This file is part of Twig.
 *
 * (c) 2009-2019 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Twig\Extensions
{
    use Twig\Extension\AbstractExtension;
    use Twig\TwigFilter;

    /**
     * @author Henrik Bjornskov <hb@peytz.dk>
     */
    class TextExtension extends AbstractExtension
    {
        public function getFilters()
        {
            return [
                new TwigFilter('truncate', 'twig_truncate_filter', ['needs_environment' => true]),
                new TwigFilter('wordwrap', 'twig_wordwrap_filter', ['needs_environment' => true]),
            ];
        }
    }
}

namespace
{
    use Twig\Environment;

    function twig_truncate_filter(Environment $env, $value, $length = 30, $preserve = false, $separator = '...')
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

    function twig_wordwrap_filter(Environment $env, $value, $length = 80, $separator = "\n", $preserve = false)
    {
        $sentences = [];

        $previous = mb_regex_encoding();
        mb_regex_encoding($env->getCharset());

        $pieces = mb_split($separator, $value);
        mb_regex_encoding($previous);

        foreach ($pieces as $piece) {
            while (!$preserve && mb_strlen($piece, $env->getCharset()) > $length) {
                $sentences[] = mb_substr($piece, 0, $length, $env->getCharset());
                $piece = mb_substr($piece, $length, 2048, $env->getCharset());
            }

            $sentences[] = $piece;
        }

        return implode($separator, $sentences);
    }
}
