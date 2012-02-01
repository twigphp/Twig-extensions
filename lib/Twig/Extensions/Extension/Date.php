<?php

/*
 * This file is part of Twig.
 *
 * (c) 2012 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Twig_Extensions_Extension_Date
 *
 * @author     Stanislav Petrov <s.e.petrov@gmail.com>
 * @package    Twig
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
        return array(
            'distance_of_time_in_words' => new Twig_Filter_Function('twig_distance_of_time_in_words_filter', array('needs_environment' => true)),
            'time_ago_in_words'         => new Twig_Filter_Function('twig_time_ago_in_words_filter', array('needs_environment' => true)),
        );
    }

    /**
     * Name of this extension
     *
     * @return string
     */
    public function getName()
    {
        return 'Date';
    }
}

/*
 * Ported from Symfony 1.4 DateHelper
 * http://svn.symfony-project.com/branches/1.4/lib/helper/DateHelper.php
 */

/**
 * Returns the distance of time in words between two points in time.
 *
 * $from_time and $to_time must be DateTime object, timestamp or string in strtotime() format.
 *
 * @param Twig_Environment $env             The environment
 * @param mixed            $from_time       The starting point
 * @param mixed            $to_time         The end point (defaults to null)
 * @param boolean          $include_seconds Whether to include the seconds (defaults to null)
 *
 * @return string
 */
function twig_distance_of_time_in_words_filter(Twig_Environment $env, $from_time, $to_time = null, $include_seconds = false)
{
    if ($from_time instanceof DateTime) {
        $from_time = $from_time->getTimestamp();
    } elseif (!is_numeric($from_time)) {
        $from_time = strtotime($from_time);
    }

    if ($to_time instanceof DateTime) {
        $to_time = $to_time->getTimestamp();
    } elseif (is_null($to_time)) {
        $to_time = time();
    } elseif (!is_numeric($to_time)) {
        $to_time = strtotime($to_time);
    }

    $distance_in_minutes = floor(abs($to_time - $from_time) / 60);
    $distance_in_seconds = floor(abs($to_time - $from_time));

    $string = '';
    $parameters = array();

    if ($distance_in_minutes <= 1) {
        if (!$include_seconds) {
            $string = $distance_in_minutes == 0 ? 'less than a minute' : '1 minute';
        } else {
            if ($distance_in_seconds <= 5) {
                $string = 'less than 5 seconds';
            } elseif ($distance_in_seconds >= 6 && $distance_in_seconds <= 10) {
                $string = 'less than 10 seconds';
            } elseif ($distance_in_seconds >= 11 && $distance_in_seconds <= 20) {
                $string = 'less than 20 seconds';
            } elseif ($distance_in_seconds >= 21 && $distance_in_seconds <= 40) {
                $string = 'half a minute';
            } elseif ($distance_in_seconds >= 41 && $distance_in_seconds <= 59) {
                $string = 'less than a minute';
            } else {
                $string = '1 minute';
            }
        }
    } elseif ($distance_in_minutes >= 2 && $distance_in_minutes <= 44) {
        $string = '%minutes% minutes';
        $parameters['%minutes%'] = $distance_in_minutes;
    } elseif ($distance_in_minutes >= 45 && $distance_in_minutes <= 89) {
        $string = 'about 1 hour';
    } elseif ($distance_in_minutes >= 90 && $distance_in_minutes <= 1439) {
        $string = 'about %hours% hours';
        $parameters['%hours%'] = round($distance_in_minutes / 60);
    } elseif ($distance_in_minutes >= 1440 && $distance_in_minutes <= 2879) {
        $string = '1 day';
    } elseif ($distance_in_minutes >= 2880 && $distance_in_minutes <= 43199) {
        $string = '%days% days';
        $parameters['%days%'] = round($distance_in_minutes / 1440);
    } elseif ($distance_in_minutes >= 43200 && $distance_in_minutes <= 86399) {
        $string = 'about 1 month';
    } elseif ($distance_in_minutes >= 86400 && $distance_in_minutes <= 525959) {
        $string = '%months% months';
        $parameters['%months%'] = round($distance_in_minutes / 43200);
    } elseif ($distance_in_minutes >= 525960 && $distance_in_minutes <= 1051919) {
        $string = 'about 1 year';
    } else {
        $string = 'over %years% years';
        $parameters['%years%'] = floor($distance_in_minutes / 525960);
    }

    if ($env->hasExtension('translator')) {
        return $env->getExtension('translator')->trans($string, $parameters);
    } else {
        return strtr($string, $parameters);
    }
}

/**
 * Returns the distance of time in words between given point in time and the now.
 *
 * $from_time must be DateTime object, timestamp or string in strtotime() format.
 *
 * @param Twig_Environment $env             The environment
 * @param mixed            $from_time       The starting point
 * @param boolean          $include_seconds Whether to include the seconds (defaults to null)
 *
 * @return string
 */
function twig_time_ago_in_words_filter(Twig_Environment $env, $from_time, $include_seconds = false)
{
    return twig_distance_of_time_in_words_filter($env, $from_time, time(), $include_seconds);
}
