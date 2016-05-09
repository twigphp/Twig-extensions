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
    private $complex_vars = false;

    /**
     * Constructor for I18n Extension.
     *
     * @param array $options Set of options for this Extension instance.
     *
     * Available options:
     *
     *  * delimiters:               When set, it defines the delimiters for
     *    [default: "{{|}}"]        field placeholders. Can be a string of
     *                              length 2 (Eg: "%%", "{}", "||") or a
     *                              string containing the mask "%s" which marks
     *                              the position of the field (Eg: "{{|}}",
     *                              "[|]").
     *
     *  * normalize:                When true, activates normalization of
     *    [default: false]          spaces inside the string. Turns all groups
     *                              of consecutive "space characters" to a
     *                              single space. ("space characters" as
     *                              defined by the "\s" regexp character class)
     *
     *  * complex_vars:             When true, activates parsing and compiling
     *    [default: false]          of "complex vars". Complex vars are those
     *                              vars comprising property accessors, array
     *                              accessors and filters. For example, a
     *                              complex var might be: {{ user.name | upper }}
     *
     *                              The resulting variable name in the
     *                              translatable string is inferred from the
     *                              original variable expression, without
     *                              filters. To avoid conflicts, repeated vars
     *                              are appended with numbers in a serie.
     *
     *                              Examples:
     *
     *                              "{{ user.name | upper }} {{ user.name | lower }}"
     *
     *                              Gets a translatable string of:
     *
     *                              "%user_name% %user_name_2%"
     *
     *                              If a inferred variable name becomes too
     *                              complex, you can use the filter "as" to
     *                              create an alias. Example:
     *
     *                              {{ report.year.status | as("total") }}
     */
    public function __construct($options = array())
    {
        $options = array_merge(array(
            'delimiters' => '%%',
            'normalize' => false,
            'complex_vars' => false,
        ), $options);

        $this->delimiters = $this->parseDelimiters($options['delimiters']);
        $this->normalize = (bool) $options['normalize'];
        $this->complex_vars = (bool) $options['complex_vars'];
    }

    /**
     * Returns the delimiters array for field placeholders in strings.
     */
    public function getDelimiters()
    {
        return $this->delimiters;
    }

    /**
     * Returns the value of the current "normalize" config.
     */
    public function getNormalize()
    {
        return $this->normalize;
    }

    /**
     * Returns the value of the current "complex_vars" config.
     */
    public function getComplexVars()
    {
        return $this->complex_vars;
    }

    /**
     * Parses the $dels to configure our "delimiters" property.
     *
     * Three syntax for delimiter patterns are allowed.
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
            return array((string) $dels[0], (string) $dels[1]);
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
             new Twig_SimpleFilter('as', null, array('node_class' => 'Twig_Extensions_Filter_As')),
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
