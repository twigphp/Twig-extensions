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
    private $delimiters = array();
    private $normalize = false;

    /**
     * Constructor for I18n Extension
     *
     * @param array     $options    Set of options for this Extension instance.
     *
     * Available options:
     *
     *  * delimiters:               When set, it defines the delimiters for
     *    [default: "{{|}}"]        field placeholders. Can be a string of
     *                              length 2 (Eg: "%%", "{}", "||") or a
     *                              string containing the mask "%s" which marks
     *                              the position of the field (Eg: "{{|}}",
     *                              "[|]").
     */
    public function __construct($options = array())
    {
        $options = array_merge(array(
            'delimiters' => '%%',
            'normalize' => false
        ), $options);

        $this->delimiters = $this->parseDelimiters($options['delimiters']);
        $this->normalize = !!$options['normalize'];
    }

    /**
     * Returns the delimiters array for field placeholders in strings
     */
    public function getDelimiters()
    {
        return $this->delimiters;
    }

    /**
     * Returns the value of the current "normalize" config
     */
    public function getNormalize()
    {
        return $this->normalize;
    }

    /**
     * Parses the $dels to configure our "delimiters" property.
     * Three syntax for delimiter patterns are allowed:
     *
     *  1) String pattern including a "|" character that marks the position of
     *     the variable name. Eg: "%|%", "start|end".
     *
     *  2) String of an even length: Delimiters are constructed with the first
     *     half and the second half. Eg: "{{}}" => ["{{", "}}"]
     *
     *  3) Array of exactly 2 items: Delimiters are constructed from the array
     *     itself. No further checking is done, but converted to strings.
     */
    private function parseDelimiters($dels)
    {
        if (is_string($dels) && (strpos($dels, '|') !== false)) {
            return array_values(array_filter(array_map('trim', explode('|', $dels))));
        }

        if (is_string($dels) && !(strlen($dels) % 2)) {
            $mid = ceil(strlen($dels) / 2);
            return array(substr($dels, 0, $mid), substr($dels, $mid));
        }

        if (is_array($dels) && (count($dels) == 2)) {
            return array((string)$dels[0], (string)$dels[1]);
        }

        throw new Twig_Error('Pattern "'.$dels.'" not allowed as delimiters.');
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
             new Twig_SimpleFilter('trans', 'gettext'),
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
