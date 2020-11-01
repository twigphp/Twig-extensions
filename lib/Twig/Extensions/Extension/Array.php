<?php

/**
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @author Ricard Clau <ricard.clau@gmail.com>
 */
class Twig_Extensions_Extension_Array extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('shuffle', 'twig_shuffle_filter'),
        ];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'array';
    }
}

/**
 * Shuffles an array.
 *
 * @param array|Traversable $array An array
 *
 * @return array
 */
function twig_shuffle_filter($array)
{
    if ($array instanceof Traversable) {
        $array = iterator_to_array($array, false);
    }

    shuffle($array);

    return $array;
}

class_alias('Twig_Extensions_Extension_Array', 'Twig\Extensions\ArrayExtension', false);
