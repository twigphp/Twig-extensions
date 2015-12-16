<?php

class Twig_Extensions_NativeGettextTranslator implements Twig_Extensions_Translator
{
    public function gettext($message)
    {
        return gettext($message);
    }

    public function ngettext($msgid1, $msgid2, $n)
    {
        return ngettext($msgid1, $msgid2, $n);
    }
}
