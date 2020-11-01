<?php

/**
 * This file is part of Twig.
 *
 * (c) 2014 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Translation\IdentityTranslator;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
class Twig_Extensions_Extension_Date extends AbstractExtension
{
    public static $units = [
        'y' => 'year',
        'm' => 'month',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    ];

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator = null)
    {
        // Ignore the IdentityTranslator, otherwise the parameters won't be replaced properly
        if ($translator instanceof IdentityTranslator) {
            $translator = null;
        }

        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('time_diff', [$this, 'diff'], ['needs_environment' => true]),
        ];
    }

    /**
     * Filter for converting dates to a time ago string like Facebook and Twitter has.
     *
     * @param Environment $env a Twig_Environment instance
     * @param string|DateTime $date a string or DateTime object to convert
     * @param string|DateTime|null $now A string or DateTime object to compare with. If none given, the current time will be used.
     *
     * @return string the converted time
     */
    public function diff(Environment $env, $date, $now = null): ?string
    {
        // Convert both dates to DateTime instances.
        $date = twig_date_converter($env, $date);
        $now = twig_date_converter($env, $now);

        // Get the difference between the two DateTime objects.
        $diff = $date->diff($now);

        // Check for each interval if it appears in the $diff object.
        foreach (self::$units as $attribute => $unit) {
            $count = $diff->$attribute;

            if (0 !== $count) {
                return $this->getPluralizedInterval($count, $diff->invert, $unit);
            }
        }

        return '';
    }

    protected function getPluralizedInterval($count, $invert, $unit): ?string
    {
        if ($this->translator) {
            $id = sprintf('diff.%s.%s', $invert ? 'in' : 'ago', $unit);

            return $this->translator->trans($id, ['%count%' => $count], 'date');
        }

        if (1 !== $count) {
            $unit .= 's';
        }

        return $invert ? "in $count $unit" : "$count $unit ago";
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'date';
    }
}

class_alias('Twig_Extensions_Extension_Date', 'Twig\Extensions\DateExtension', false);
