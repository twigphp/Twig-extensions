<?php

/**
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Andrey Kryukov <a.kryukov.nor@gmail.com>
 * @package Twig
 * @subpackage Twig-extensions
 */
class Twig_Extensions_Extension_Date extends Twig_Extension
{
    /**
     * Returns a list of filters.
     *
     * @return array
     */
    public function getFilters()
    {
        $filters = array(
             new Twig_SimpleFilter('age', 'twig_age_filter'),
        );

        return $filters;
    }
    /**
     * Name of this extension
     *
     * @return string
     */
    public function getName()
    {
        return 'date';
    }
}

/**
 * Returns age depending on birthdate.
 *
 * @param DateTime|string  $birthdate
 * @return integer
 */
function twig_age_filter($birthdate)
{
    if (!$birthdate instanceof \DateTime) $birthdate = new \DateTime($birthdate);
    $age = $birthdate->diff(new \DateTime())->y;

    return $age;
}