<?php

class Twig_Extensions_Extension_Indent extends Twig_Extension
{
    protected $indent_string;
    protected $start_tags;
    protected $end_tags;

    public function __construct($indent_string = "\t", $options = array())
    {
        $this->indent_string = $indent_string;

        $this->start_tags = array(
            'autoescape',
            'block',
            'embed',
            'filter',
            'for',
            'if',
            'macro',
            'sandbox',
            'spaceless',
            'verbatim',
            'else',
            'elseif',
        );

        $this->end_tags = array(
            'endautoescape',
            'endblock',
            'endembed',
            'endfilter',
            'endfor',
            'endif',
            'endmacro',
            'endsandbox',
            'endspaceless',
            'endverbatim',
            'else',
            'elseif',
        );

        if (isset($options['start_tags'])) {
            $this->start_tags = array_merge($this->start_tags, $options['start_tags']);
        }

        if (isset($options['end_tags'])) {
            $this->end_tags = array_merge($this->end_tags, $options['end_tags']);
        }
    }

    public function getName()
    {
        return 'indent';
    }

    public function getIndentString()
    {
        return $this->indent_string;
    }

    public function getStartTags()
    {
        return $this->start_tags;
    }

    public function getEndTags()
    {
        return $this->end_tags;
    }

    public function getFilters()
    {
        $ch = $this->indent_string;

        return array(
            new Twig_SimpleFilter(
                'indent',
                function ($str, $n, $skip_first = true) use ($ch) {
                    $prefix = str_repeat($ch, $n);
                    $lines = explode("\n", $str);

                    if ($skip_first) {
                        $first = array_shift($lines);

                        if (end($lines) === '') {
                            array_pop($lines);
                        }
                    }

                    $lines = array_map(function ($s) use ($prefix) { return $prefix.$s; }, $lines);

                    if ($skip_first) {
                        array_unshift($lines, $first);
                    }

                    return implode("\n", $lines);
                }
            ),
            new Twig_SimpleFilter(
                'unindent',
                function ($str, $n) use ($ch) {
                    $prefix = str_repeat($ch, $n);
                    $len = strlen($prefix);
                    $lines = explode("\n", $str);

                    $lines = array_map(
                        function ($s) use ($prefix,$len) {
                            return strncmp($s, $prefix, $len) === 0 ? substr($s, $len) : $s;
                        },
                        $lines
                    );

                    return implode("\n", $lines);
                }
            ),
        );
    }
}
