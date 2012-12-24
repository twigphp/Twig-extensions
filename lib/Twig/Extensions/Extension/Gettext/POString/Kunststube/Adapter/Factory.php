<?php

class Twig_Extensions_Extension_Gettext_POString_Kunststube_Adapter_Factory
    implements Twig_Extensions_Extension_Gettext_POString_Factory_Interface {
    
    public function construct($msgid, $msgidPlural = null) {
        return new Twig_Extensions_Extension_Gettext_POString_Kunststube_Adapter($msgid, $msgidPlural);
    }
    
}