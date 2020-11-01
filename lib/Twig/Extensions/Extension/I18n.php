<?php

/*
 * This file is part of Twig.
 *
 * (c) 2010 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class Twig_Extensions_Extension_I18n extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getTokenParsers(): array
    {
        return [new Twig_Extensions_TokenParser_Trans()];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('trans', 'gettext'),
        ];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'i18n';
    }
}

class_alias('Twig_Extensions_Extension_I18n', 'Twig\Extensions\I18nExtension', false);
