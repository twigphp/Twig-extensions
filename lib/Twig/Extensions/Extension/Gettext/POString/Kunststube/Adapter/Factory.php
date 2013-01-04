<?php

/**
 * A factory for Twig_Extensions_Extension_Gettext_POString_Kunststube_Adapter objects.
 */
class Twig_Extensions_Extension_Gettext_POString_Kunststube_Adapter_Factory
    implements Twig_Extensions_Extension_Gettext_POString_Factory_Interface {
    
    public function construct($msgid) {
        return new Twig_Extensions_Extension_Gettext_POString_Kunststube_Adapter($msgid);
    }
    
}