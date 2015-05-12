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
     * Name of this extension.
     *
     * @return string
     */
    public function getName()
    {
        return 'Text';
    }
}

if (function_exists('mb_get_info')) {
    function twig_truncate_filter(Twig_Environment $env, $value, $limit = 30, $preserve = false, $separator = '...')
    {
        if (mb_strlen($value, $env->getCharset()) > $limit) {
            $separatorLength = mb_strlen($separator, $env->getCharset());

            if ($limit < $separatorLength) {
                throw new Twig_Error_Syntax('The separator cannot be longer than your limit!');
            }

            // Special case:
            // Our examination-length is longer than the actual limit.
            // So we just return our separator.
            if ($limit - $separatorLength - 1 <= 0) {
                return $separator;
            }

            if ($preserve) {
                // Determine the length of our separator.
                $examinedString = mb_substr($value, 0, $limit);
                $lastChar = mb_substr($value, mb_strlen($examinedString) + 1, 1);

                // If we do not find any whitespace in $examinedString starting from our string,
                // minus the separatorlength and minus one character, then we know that this
                // word is longer than the separator. So we have to cut off the string before
                // that.
                $lastWordStart = mb_strpos($examinedString, ' ', $limit - $separatorLength - 1, $env->getCharset());
                // Now we have to find the position of the last whitespace. This may be a
                // problem, if we have a pretty long separator.
                $lastWordEnd = mb_strrpos($examinedString, ' ', $limit, $env->getCharset());

                if (false === $lastWordStart && false === $lastWordEnd) {
                    // Case 1: No last word found
                    $limit = mb_strrpos($examinedString, ' ', 0);
                } elseif (false !== $lastWordStart && false === $lastWordEnd && $lastChar !== ' ') {
                    // Case 2: Beginning of last word found, but no end
                    $limit = $lastWordStart;
                } elseif (false !== $lastWordStart && false === $lastWordEnd && $lastChar === ' ') {
                    // Case 3: Beginning of last word found, and it fits right in our limit.
                    $limit = $lastWordEnd + 1;
                } else {
                    // Case 4: It just fits in our boundaries.
                    $limit = $lastWordEnd;
                }
            } else {
                $limit = $limit - $separatorLength;
            }

            return rtrim(mb_substr($value, 0, $limit, $env->getCharset())).$separator;
        }

        return $value;
    }

    function twig_wordwrap_filter(Twig_Environment $env, $value, $length = 80, $separator = "\n", $preserve = false)
    {
        $sentences = array();

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
} else {
    function twig_truncate_filter(Twig_Environment $env, $value, $limit = 30, $preserve = false, $separator = '...')
    {
        if (strlen($value) > $limit) {
            $separatorLength = strlen($separator);

            if ($limit < $separatorLength) {
                throw new Twig_Error_Syntax('The separator cannot be longer than your limit!');
            }

            // Special case:
            // Our examination-length is longer than the actual limit.
            // So we just return our separator.
            if ($limit - $separatorLength - 1 <= 0) {
                return $separator;
            }

            if ($preserve) {
                // Determine the length of our separator.
                $examinedString = substr($value, 0, $limit);
                $lastChar = substr($value, strlen($examinedString) + 1, 1);

                // If we do not find any whitespace in $examinedString starting from our string,
                // minus the separatorlength and minus one character, then we know that this
                // word is longer than the separator. So we have to cut off the string before
                // that.
                $lastWordStart = strpos($examinedString, ' ', $limit - $separatorLength - 1);
                // Now we have to find the position of the last whitespace. This may be a
                // problem, if we have a pretty long separator.
                $lastWordEnd = strrpos($examinedString, ' ', $limit);

                if (false === $lastWordStart && false === $lastWordEnd) {
                    // Case 1: No last word found
                    $limit = strrpos($examinedString, ' ', 0);
                } elseif (false !== $lastWordStart && false === $lastWordEnd && $lastChar !== ' ') {
                    // Case 2: Beginning of last word found, but no end
                    $limit = $lastWordStart;
                } elseif (false !== $lastWordStart && false === $lastWordEnd && $lastChar === ' ') {
                    // Case 3: Beginning of last word found, and it fits right in our limit.
                    $limit = $lastWordEnd + 1;
                } else {
                    // Case 4: It just fits in our boundaries.
                    $limit = $lastWordEnd;
                }
            } else {
                $limit = $limit - $separatorLength;
            }

            return rtrim(substr($value, 0, $limit)).$separator;
        }

        return $value;
    }

    function twig_wordwrap_filter(Twig_Environment $env, $value, $length = 80, $separator = "\n", $preserve = false)
    {
        return wordwrap($value, $length, $separator, !$preserve);
    }
}
