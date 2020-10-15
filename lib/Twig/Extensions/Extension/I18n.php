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
     * Holds the current options for I18n extension.
     *
     * @see Twig_Extensions_Node_Trans_Options
     */
    private $options;

    /**
     * Constructor for I18n Extension.
     *
     * @param array $options Set of options for this Extension instance.
     * @see Twig_Extensions_Node_Trans_Options
     */
    public function __construct($options = array())
    {
        $this->options = new Twig_Extensions_Node_Trans_Options($options);
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenParsers()
    {
        return array(new Twig_Extensions_TokenParser_Trans($this->options));
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
             new Twig_SimpleFilter('trans', 'gettext'),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'i18n';
    }
}

class_alias('Twig_Extensions_Extension_I18n', 'Twig\Extensions\I18nExtension', false);
