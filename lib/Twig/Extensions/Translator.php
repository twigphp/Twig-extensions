<?php

interface Twig_Extensions_Translator
{
    public function gettext($message);
    public function ngettext($msgid1, $msgid2, $n);
}
