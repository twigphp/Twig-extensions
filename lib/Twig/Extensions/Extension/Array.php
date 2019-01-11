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
             new Twig_SimpleFilter('merge_recursive', 'twig_array_merge_recursive'),
             new Twig_SimpleFilter('shuffle', 'twig_shuffle_filter'),
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
 * Recursivly merges an array with another one.
 *
 * <pre>
 *  {% set items = { 'fruits': ['apple', 'orange'], 'car': 'peugeot' } %}
 *
 *  {% set items = items|merge_recursive({ 'fruits': 'banana', 'car': [ 'renault', 'citroen' ] }) %}
 *
 *  {# items now contains { 'fruits': ['apple', 'orange', 'banana'], 'car': [ 'peugeot', 'renault', 'citroen' ] } #}
 * </pre>
 *
 * @param array|Traversable $arr1 An array
 * @param array|Traversable $arr2 An array
 *
 * @return array The merged array
 */
function twig_array_merge_recursive($arr1, $arr2)
{
    if (!is_iterable($arr1)) {
        throw new Twig_Error_Runtime(
            sprintf(
                'The merge_recursive filter only works with arrays or "Traversable", got "%s" as first argument.',
                gettype($arr1)
            )
        );
    }

    if (!is_iterable($arr2)) {
        throw new Twig_Error_Runtime(
            sprintf(
                'The merge_recursive filter only works with arrays or "Traversable", got "%s" as second argument.',
                gettype($arr2)
            )
        );
    }

    return array_merge_recursive(twig_recursive_cast_to_array($arr1), twig_recursive_cast_to_array($arr2));
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
 * @internal
 */
function twig_recursive_cast_to_array($input)
{
    if ($input instanceof Traversable) {
        return array_map('twig_recursive_cast_to_array', iterator_to_array($input));
    }

    if (is_array($input)) {
        return array_map('twig_recursive_cast_to_array', $input);
    }

    return $input;
}

class_alias('Twig_Extensions_Extension_Array', 'Twig\Extensions\ArrayExtension', false);
