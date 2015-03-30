The Indent Extension
====================

The *Indent* extension provides ``indent`` and ``unindent`` filters along with a custom lexer
that improves the output indentation.

To enable this extension you need to register it and setup the provided lexer::

    $twig->addExtension(new Twig_Extensions_Extension_Indent());
    $twig->setLexer(new Twig_Extensions_IndentLexer($twig));

By default it will use tab characters for indentation but this can be configured by passing
a string to the extension constructor::

    $twig->addExtension(new Twig_Extensions_Extension_Indent('  '));

This extension will automatically detect the default tags that have a matching "end" tag and
unindent its contents. There's also an option to extend the list of detected tags::

    $twig->addExtension(new Twig_Extensions_Extension_Indent("\t", array(
        'start_tags' => array('customtag'),
        'end_tags' => array('endcustomtag')
    )));

How it works
------------

Given the following template::

    {% set variable = "line1\nline2\nline3" %}
    <body>
        {% if true %}
            <stuff>{% if true %}only blocks that take the whole line have effect{% endif %}</stuff>
            {{ variable }}
        {% endif %}
    </body>

This would be the output::

    <body>
        <stuff>only blocks that take the whole line have effect</stuff>
        line1
        line2
        line3
    </body>

The lexer will first run the default Twig lexer. Next, it will search for all ``{% %}`` and
``{{ }}`` pairs that take a whole line (only preceded by whitespace and immediately followed
by a new line). I call those "line blocks" and "line expression blocks" respectively.

The text around line blocks (``{% %}``) is modified in a way that it basically results
in the line being removed from the output (it removes the preceding whitespace and the
following new line).

Tags that have a matching ending tag (like ``if..endif``) get injected with ``unindent``
filter tags, as long as they are line blocks. Note that for this to work properly both tags
must be line blocks. The lexer will inject a ``filter`` tag after every starting line block and
an ``endfilter`` tag before every ending line block, but it's not smart enough to know which
ending tag belongs with which starting tag.

Line *expression* blocks preserve the whitespace around them, but they are wrapped around
``indent`` filters.

To illustrate how it all works, the template from the example can be seen like this:

    {% set variable = "line1\nline2\nline3" %}
    <body>
        {% if true %}
        {% filter unindent(1) %}
            <stuff>{% if true %}only blocks that take the whole line have effect{% endif %}</stuff>
            {% filter indent(2) %}{{ variable }}{% endfilter %}
        {% endfilter %}
        {% endif %}
    </body>

Note that ``{{ variable }}`` gets wrapped inside an ``indent`` filter and it automatically
detects the indentation (``2``) from the preceding whitespace. This filter won't indent the
first line of the content and will also remove the last empty line from the filtered content.
This behaviour can be changed by passing ``false`` as the second argument, if used manually.
