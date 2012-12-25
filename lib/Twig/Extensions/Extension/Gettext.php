<?php

class Twig_Extensions_Extension_Gettext extends Twig_Extension {
    
    protected $useShortnames = true;
    
    public function __construct($useShortnames = true) {
        $this->useShortnames = $useShortnames;
    }
    
    public function getName() {
        return 'gettext';
    }
    
    public function getFunctions() {
        $gettext     = new Twig_Function_Function('gettext');
        $pgettext    = new Twig_Function_Method($this, 'pgettext');
        $ngettext    = new Twig_Function_Function('ngettext');
        $npgettext   = new Twig_Function_Method($this, 'npgettext');
        $dgettext    = new Twig_Function_Function('dgettext');
        $dpgettext   = new Twig_Function_Method($this, 'dpgettext');
        $dngettext   = new Twig_Function_Function('dngettext');
        $dnpgettext  = new Twig_Function_Method($this, 'dnpgettext');
        $dcgettext   = new Twig_Function_Method($this, 'dcgettext');
        $dcpgettext  = new Twig_Function_Method($this, 'dcpgettext');
        $dcngettext  = new Twig_Function_Method($this, 'dcngettext');
        $dcnpgettext = new Twig_Function_Method($this, 'dcnpgettext');
        
        $functions = array(
            'gettext'     => $gettext,
            'pgettext'    => $pgettext,
            'ngettext'    => $ngettext,
            'npgettext'   => $npgettext,
            'dgettext'    => $dgettext,
            'dpgettext'   => $dpgettext,
            'dngettext'   => $dngettext,
            'dnpgettext'  => $dnpgettext,
            'dcgettext'   => $dcgettext,
            'dcpgettext'  => $dcpgettext,
            'dcngettext'  => $dcngettext,
            'dcnpgettext' => $dcnpgettext
        );
        
        if ($this->useShortnames) {
            $functions += array(
                '_'     => $gettext,
                '_p'    => $pgettext,
                '_n'    => $ngettext,
                '_np'   => $npgettext,
                '_d'    => $dgettext,
                '_dp'   => $dpgettext,
                '_dn'   => $dngettext,
                '_dnp'  => $dnpgettext,
                '_dc'   => $dcgettext,
                '_dcp'  => $dcpgettext,
                '_dcn'  => $dcngettext,
                '_dcnp' => $dcnpgettext
            );
        }
        
        return $functions;
    }
    
    public function getFilters() {
        $gettext     = new Twig_Filter_Function('gettext');
        $pgettext    = new Twig_Filter_Method($this, 'pgettextFilter');
        $ngettext    = new Twig_Filter_Function('ngettext');
        $npgettext   = new Twig_Filter_Method($this, 'npgettextFilter');
        $dgettext    = new Twig_Filter_Method($this, 'dgettextFilter');
        $dpgettext   = new Twig_Filter_Method($this, 'dpgettextFilter');
        $dngettext   = new Twig_Filter_Method($this, 'dngettextFilter');
        $dnpgettext  = new Twig_Filter_Method($this, 'dnpgettextFilter');
        $dcgettext   = new Twig_Filter_Method($this, 'dcgettextFilter');
        $dcpgettext  = new Twig_Filter_Method($this, 'dcpgettextFilter');
        $dcngettext  = new Twig_Filter_Method($this, 'dcngettextFilter');
        $dcnpgettext = new Twig_Filter_Method($this, 'dcnpgettextFilter');
        
        $filters = array(
            'gettext'     => $gettext,
            'pgettext'    => $pgettext,
            'ngettext'    => $ngettext,
            'npgettext'   => $npgettext,
            'dgettext'    => $dgettext,
            'dpgettext'   => $dpgettext,
            'dngettext'   => $dngettext,
            'dnpgettext'  => $dnpgettext,
            'dcgettext'   => $dcgettext,
            'dcpgettext'  => $dcpgettext,
            'dcngettext'  => $dcngettext,
            'dcnpgettext' => $dcnpgettext,
            'sprintf'     => new Twig_Filter_Method($this, 'sprintf')
        );
        
        if ($this->useShortnames) {
            $filters += array(
                '_'     => $gettext,
                '_p'    => $pgettext,
                '_n'    => $ngettext,
                '_np'   => $npgettext,
                '_d'    => $dgettext,
                '_dp'   => $dpgettext,
                '_dn'   => $dngettext,
                '_dnp'  => $dnpgettext,
                '_dc'   => $dcgettext,
                '_dcp'  => $dcpgettext,
                '_dcn'  => $dcngettext,
                '_dcnp' => $dcnpgettext
            );
        }
        
        return $filters;
    }
    
    public function pgettext($context, $message) {
        return gettext($context . "\04" . $message);
    }
    
    public function npgettext($context, $msgid1, $msgid2, $n) {
        return ngettext($context . "\04" . $msgid1, $context . "\04" . $msgid2, $n);
    }
    
    public function dpgettext($context, $domain, $message) {
        return dgettext($domain, $context . "\04" . $message);
    }

    public function dnpgettext($context, $domain, $msgid1, $msgid2, $n) {
        return dngettext($domain, $context . "\04" . $msgid1, $context . "\04" . $msgid2, $n);
    }

    public function dcgettext($domain, $message, $category) {
        return dcgettext($domain, $message, constant($category));
    }
    
    public function dcpgettext($context, $domain, $message, $category) {
        return dcgettext($domain, $context . "\04" . $message, constant($category));
    }
    
    public function dcngettext($domain, $msgid1, $msgid2, $n, $category) {
        return dcngettext($domain, $msgid1, $msgid2, $n, constant($category));
    }
    
    public function dcnpgettext($context, $domain, $msgid1, $msgid2, $n, $category) {
        return dcngettext($domain, $context . "\04" . $msgid1, $context . "\04" . $msgid2, $n, constant($category));
    }
    
    public function pgettextFilter($message, $context) {
        return $this->pgettext($context, $message);
    }
    
    public function npgettextFilter($msgid1, $msgid2, $n, $context) {
        return $this->npgettext($context, $msgid1, $msgid2, $n);
    }
    
    public function dgettextFilter($message, $domain) {
        return dgettext($domain, $message);
    }
    
    public function dpgettextFilter($message, $domain, $context) {
        return $this->dpgettext($context, $domain, $message);
    }
    
    public function dngettextFilter($msgid1, $msgid2, $n, $domain) {
        return dngettext($domain, $msgid1, $msgid2, $n);
    }
    
    public function dnpgettextFilter($msgid1, $msgid2, $n, $domain, $context) {
        return $this->dnpgettext($context, $domain, $msgid1, $msgid2, $n);
    }
    
    public function dcgettextFilter($message, $domain, $category) {
        return $this->dcgettext($domain, $message, $category);
    }
    
    public function dcpgettextFilter($message, $domain, $category, $context) {
        return $this->dcpgettext($context, $domain, $message, $category);
    }
    
    public function dcngettextFilter($msgid1, $msgid2, $n, $domain, $category) {
        return $this->dcngettext($domain, $msgid1, $msgid2, $n, $category);
    }
    
    public function dcnpgettextFilter($msgid1, $msgid2, $n, $domain, $category, $context) {
        return $this->dcngettext($context, $domain, $msgid1, $msgid2, $n, $category);
    }
    
    public function sprintf($string /*, $arg, ... */) {
        $args = func_get_args();
        return vsprintf($string, array_slice($args, 1));
    }
    
}