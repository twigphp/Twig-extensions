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
     * @author Ricard Clau <ricard.clau@gmail.com>
     */
    class ArrayExtension extends AbstractExtension
    {
        public function getFilters()
        {
            return array(
                new TwigFilter('shuffle', '\twig_shuffle_filter'),
            );
        }
    }
}

namespace
{
    /**
     * Shuffles an array.
     *
     * @param array|\Traversable $array An array
     *
     * @return array
     */
    function twig_shuffle_filter($array)
    {
        if ($array instanceof \Traversable) {
            $array = iterator_to_array($array, false);
        }

        shuffle($array);

        return $array;
    }
}
