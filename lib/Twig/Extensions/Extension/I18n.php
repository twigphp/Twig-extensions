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
    /** @var Twig_Extensions_Translator */
    private $translator;

    public function __construct(Twig_Extensions_Translator $translator = null)
    {
        if ($translator === null) {
            $this->translator = new Twig_Extensions_NativeGettextTranslator();
        } else {
            $this->translator = $translator;
        }
    }

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
             new Twig_SimpleFilter('trans', array($this->translator, 'gettext')),
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

    /**
     * @return Twig_Extensions_Translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }
}
