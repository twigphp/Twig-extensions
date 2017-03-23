<?php
/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This extension provides access to static methods from the \Locale class.
 *
 * @author Matthias Pigulla <mp@webfactory.de>
 */
class Twig_Extensions_Extension_Locale extends Twig_Extension
{
    public function __construct()
    {
        if (!class_exists('Locale')) {
            throw new RuntimeException('The PHP intl extension or the symfony/intl replacement layer needed to use the locale extension.');
        }
    }

    public function getFunctions()
    {
        return array(
            new Twig_SimpleFunction('locale_primary_language', 'twig_locale_primary_language'),
        );
    }
}

function twig_locale_primary_language($locale = null)
{
    $locale = $locale !== null ? $locale : Locale::getDefault();

    return Locale::getPrimaryLanguage($locale);
}
