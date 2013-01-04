<?php

/**
 * An adapter to use the Kunststube\POTools\POString object as
 * POString object for Twig_Extensions_Extension_Gettext_Extractor.
 * See https://github.com/deceze/Kunststube-POTools.
 */
class Twig_Extensions_Extension_Gettext_POString_Kunststube_Adapter
    extends Kunststube\POTools\POString
    implements Twig_Extensions_Extension_Gettext_POString_Interface {
        
    public function __construct($msgid) {
        parent::__construct($msgid);
    }
        
}