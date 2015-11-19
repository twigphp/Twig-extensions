<?php

/*
 * This file is part of Twig.
 *
 * (c) 2010 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Twig_Extensions_Extension_I18n extends Twig_Extension
{
    /**
     * Returns the token parser instances to add to the existing list.
     *
     * @return array An array of Twig_TokenParserInterface or Twig_TokenParserBrokerInterface instances
     */
    public function getTokenParsers()
    {
        return array(new Twig_Extensions_TokenParser_Trans());
    }

    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return array An array of filters
     */
    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter('trans', 'gettext'),
            new Twig_SimpleFilter('ptrans', 'twig_pgettext'),
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'i18n';
    }
}

if(!function_exists('twig_pgettext')){
    /**
     * Gets a translation checking the context.
     *
     * @param string $original
     * @param string $context
     *
     * @return string
     */
    function twig_pgettext($original,$context)
    {
        $message = "{$context}\004{$original}";
        $translation = gettext($message);

        return ($translation === $message) ? $original : $translation;
    }
}

if(!function_exists('twig_npgettext')){
    /**
     * Gets a translation checking the context and the plural form.
     *
     * @param string $original
     * @param string $plural
     * @param string $value
     * @param string $context
     *
     * @return string
     */
    function twig_npgettext($original, $plural, $value, $context)
    {
        $message = "{$context}\004{$original}";
        $translation = ngettext($message, $plural, $value);

        return ($translation === $message) ? $original : $translation;
    }
}