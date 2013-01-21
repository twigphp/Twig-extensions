<?php

/**
 * This file is part of Twig.
 *
 * (c) 2014 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 * @package Twig
 * @subpackage Twig-extensions
 */

use Symfony\Component\Translation\TranslatorInterface;

class Twig_Extensions_Extension_Date extends Twig_Extension
{
    static $units = array(
        'y' => 'year',
        'm' => 'month',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * Constructor.
     *
     * @param TranslatorInterface $translator A TranslatorInterface instance.
     */
    public function __construct(TranslatorInterface $translator = null)
    {
        $this->translator = $translator;
    }

    /**
     * Returns a list of filters.
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter('time_diff', array($this, 'diff')),
        );
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

    /**
     * Filter for converting dates to a time ago string like Facebook and Twitter has.
     *
     * @param string|DateTime $date A string or DateTime object to convert.
     * @param string|DateTime $now  A string or DateTime object to compare with. If none given, the current time will be used.
     *
     * @return string The converted time.
     */
    public function diff($date, $now = null)
    {
        // Convert the $date variable to a DateTime object if it's a string.
        if (is_string($date)) {
            $date = new DateTime($date);
        }

        if (!$date instanceof DateTime) {
            throw new InvalidArgumentException('$date must be a valid DateTime instance');
        }

        // If no $now variable is given, use the current time.
        if (!$now) {
            $now = new DateTime();
        } elseif (is_string($now)) {
            // Else if it's a string, convert it to a DateTime object.
            $now = new DateTime($now);
        }

        if (!$now instanceof DateTime) {
            throw new InvalidArgumentException('$now must be a valid DateTime instance');
        }

        // Get the difference between the two DateTime objects.
        $diff = method_exists($date, 'diff') ? $date->diff($now) : $this->diffDates($date, $now);

        // Check for each interval if it appears in the $diff object.
        foreach (self::$units as $attribute => $unit) {
            $count = $diff->$attribute;

            if (0 !== $count) {
                return $this->getPluralizedInterval($count, $diff->invert, $unit);
            }
        }

        return '';
    }

    /**
     * Get the differences between two DateTime objects.
     *
     * Function is a replacement for DateTime::diff because Twig needs to support PHP 5.2.
     */
    protected function diffDates(DateTime $datetime1, DateTime $datetime2)
    {
        $diff = new stdClass();

        if ($datetime1 > $datetime2) {
            $tmp = $datetime1;
            $datetime1 = $datetime2;
            $datetime2 = $tmp;
            $diff->invert = 1;
        } else {
            $diff->invert = 0;
        }

        $diff->y = ((int) $datetime2->format('Y')) - ((int) $datetime1->format('Y'));
        $diff->m = ((int) $datetime2->format('n')) - ((int) $datetime1->format('n'));

        if ($diff->m < 0) {
            $diff->y -= 1;
            $diff->m = $diff->m + 12;
        }

        $diff->d = ((int) $datetime2->format('j')) - ((int) $datetime1->format('j'));
        if ($diff->d < 0) {
            $diff->m -= 1;
            $diff->d = $diff->d + ((int) $datetime1->format('t'));
        }

        $diff->h = ((int) $datetime2->format('G')) - ((int) $datetime1->format('G'));
        if ($diff->h < 0) {
            $diff->d -= 1;
            $diff->h = $diff->h + 24;
        }

        $diff->i = ((int) $datetime2->format('i')) - ((int) $datetime1->format('i'));
        if ($diff->i < 0) {
            $diff->h -= 1;
            $diff->i = $diff->i + 60;
        }

        $diff->s = ((int) $datetime2->format('s')) - ((int) $datetime1->format('s'));
        if ($diff->s < 0) {
            $diff->i -= 1;
            $diff->s = $diff->s + 60;
        }

        $startTs = $datetime1->format('U');
        $endTs = $datetime2->format('U');

        $days = $endTs - $startTs;
        $diff->days = round($days / 86400);

        if (($diff->h > 0 || $diff->i > 0 || $diff->s > 0)) {
            $diff->days += ((bool) $diff->invert) ? 1 : -1;
        }

        return $diff;
    }

    protected function getPluralizedInterval($count, $invert, $unit)
    {
        if ($this->translator) {
            $id = sprintf('diff.%s.%s', $invert ? 'in' : 'ago', $unit);

            return $this->translator->transChoice($id, $count, array('%count%' => $count), 'date');
        }

        if ($count > 1) {
            $unit .= 's';
        }

        return $invert ? "in $count $unit" : "$count $unit ago";
    }
}
