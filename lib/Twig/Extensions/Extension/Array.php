<?php

/**
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Ricard Clau <ricard.clau@gmail.com>
 */
class Twig_Extensions_Extension_Array extends Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        $filters = array(
             new Twig_SimpleFilter('shuffle', 'twig_shuffle_filter'),
             new Twig_SimpleFilter('unset', 'twig_unset_filter'),
        );

        return $filters;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
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

/**
 * Unset a key from an array.
 *
 * @param array      $array An array
 * @param string|int $key   An array key
 *
 * @return array
 */
function twig_unset_filter($array, $key)
{
    unset($array[$key]);

    return $array;
}
