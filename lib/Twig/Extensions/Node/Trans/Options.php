<?php

/*
 * This file is part of Twig.
 *
 * (c) 2010 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Represents a trans node options.
 *
 * @author Fabien Potencier <fabien.potencier@symfony-project.com>
 */
class Twig_Extensions_Node_Trans_Options
{
    /**
     *  "delimiters" option:    When set, it defines the delimiters for field
     *  [default: "%%"]         placeholders. Can be a string of length 2
     *                          (Eg: "%%", "{}", "||") or a string containing
     *                          the mask "|" which marks the position of the
     *                          field (Eg: "{{|}}", "[|]").
     */
    private $delimiters = array();

    /**
     *  "normalize" option:     When true, activates normalization of spaces
     *  [default: false]        inside the string. Turns all groups of
     *                          consecutive "space characters" to a single
     *                          space. ("space characters" as defined by the
     *                          "\s" regexp character class).
     */
    private $normalize = false;

    /**
     *  "complex_vars" option:  When true, activates parsing and compiling of
     *  [default: false]        "complex vars". Complex vars are those vars
     *                          comprising property accessors, array accessors
     *                          and filters. For example, a complex var might
     *                          be: {{ user.name|upper }}
     *
     *                          The resulting variable name in the translatable
     *                          string is inferred from the original variable
     *                          expression, without filters. To avoid conflicts,
     *                          repeated vars are appended with numbers in
     *                          sequential order.
     *
     *                          Examples:
     *
     *                          "{{ user.name|upper }} {{ user.name|lower }}"
     *
     *                          Gets a translatable string of:
     *
     *                          "%user_name% %user_name_2%"
     *
     *                          If a inferred variable name becomes too complex,
     *                          you can use the filter "as" to create an alias.
     *
     *                          Example:
     *
     *                          {{ report.year.status|as("total") }}
     */
    private $complexVars = false;

    /**
     * Constructor for Node Trans Options.
     *
     * @param array $options Set of options from this Extension instance.
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
        $this->complexVars = (bool) $options['complex_vars'];
    }

    /**
     * Returns the delimiters array for field placeholders in strings.
     */
    public function getDelimiters()
    {
        return $this->delimiters;
    }

    /**
     * Returns the value of the current "normalize" option.
     */
    public function getNormalize()
    {
        return $this->normalize;
    }

    /**
     * Returns the value of the current "complexVars" option.
     */
    public function getComplexVars()
    {
        return $this->complexVars;
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

}
