<?php
/**
 * Twig Extension to add the UnCamelCaseFilter
 */
class Twig_Extensions_Extension_UnCamelCase extends Twig_Extension
{
    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'un_came_case';
    }

    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFilters()
    {
        return array(
            'un_camel_case' => new Twig_Filter_Method($this, 'unCamelCaseString'),
        );
    }

    /**
     * Converts a camel case string into a string separated with spaces
     *
     * @param  string  $string
     * @return string
     */
    public function unCamelCaseString($string)
    {
        $newString = preg_replace('`([A-Z])`', ' $1', $string);
        return strtolower($newString);
    }
}