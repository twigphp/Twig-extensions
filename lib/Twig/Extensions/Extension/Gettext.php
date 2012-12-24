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
        $gettext      = new Twig_Function_Function('gettext');
        $fgettext     = new Twig_Function_Method($this, 'fgettext');
        $pgettext     = new Twig_Function_Method($this, 'pgettext');
        $pfgettext    = new Twig_Function_Method($this, 'pfgettext');
        $ngettext     = new Twig_Function_Function('ngettext');
        $nfgettext    = new Twig_Function_Method($this, 'nfgettext');
        $npgettext    = new Twig_Function_Method($this, 'npgettext');
        $npfgettext   = new Twig_Function_Method($this, 'npfgettext');
        $dgettext     = new Twig_Function_Function('dgettext');
        $dfgettext    = new Twig_Function_Method($this, 'dfgettext');
        $dpgettext    = new Twig_Function_Method($this, 'dpgettext');
        $dpfgettext   = new Twig_Function_Method($this, 'dpfgettext');
        $dngettext    = new Twig_Function_Function('dngettext');
        $dnfgettext   = new Twig_Function_Method($this, 'dnfgettext');
        $dnpgettext   = new Twig_Function_Method($this, 'dnpgettext');
        $dnpfgettext  = new Twig_Function_Method($this, 'dnpfgettext');
        $dcgettext    = new Twig_Function_Function('dcgettext');
        $dcfgettext   = new Twig_Function_Method($this, 'dcfgettext');
        $dcpgettext   = new Twig_Function_Method($this, 'dcpgettext');
        $dcpfgettext  = new Twig_Function_Method($this, 'dcpfgettext');
        $dcngettext   = new Twig_Function_Function('dcngettext');
        $dcnfgettext  = new Twig_Function_Method($this, 'dcnfgettext');
        $dcnpgettext  = new Twig_Function_Method($this, 'dcnpgettext');
        $dcnpfgettext = new Twig_Function_Method($this, 'dcnpfgettext');
        
        $functions = array(
            'gettext'      => $gettext,
            'fgettext'     => $fgettext,
            'pgettext'     => $pgettext,
            'pfgettext'    => $pfgettext,
            'ngettext'     => $ngettext,
            'nfgettext'    => $nfgettext,
            'npgettext'    => $npgettext,
            'npfgettext'   => $npfgettext,
            'dgettext'     => $dgettext,
            'dfgettext'    => $dfgettext,
            'dpgettext'    => $dpgettext,
            'dpfgettext'   => $dpfgettext,
            'dngettext'    => $dngettext,
            'dnfgettext'   => $dnfgettext,
            'dnpgettext'   => $dnpgettext,
            'dnpfgettext'  => $dnpfgettext,
            'dcgettext'    => $dcgettext,
            'dcfgettext'   => $dcfgettext,
            'dcpgettext'   => $dcpgettext,
            'dcpfgettext'  => $dcpfgettext,
            'dcngettext'   => $dcngettext,
            'dcnfgettext'  => $dcnfgettext,
            'dcnpgettext'  => $dcnpgettext,
            'dcnpfgettext' => $dcnpfgettext
        );
        
        if ($this->useShortnames) {
            $functions += array(
                '_'      => $gettext,
                '_f'     => $fgettext,
                '_p'     => $pgettext,
                '_pf'    => $pfgettext,
                '_n'     => $ngettext,
                '_nf'    => $nfgettext,
                '_np'    => $npgettext,
                '_npf'   => $npfgettext,
                '_d'     => $dgettext,
                '_df'    => $dfgettext,
                '_dp'    => $dpgettext,
                '_dpf'   => $dpfgettext,
                '_dn'    => $dngettext,
                '_dnf'   => $dnfgettext,
                '_dnp'   => $dnpgettext,
                '_dnpf'  => $dnpfgettext,
                '_dc'    => $dcgettext,
                '_dcf'   => $dcfgettext,
                '_dcp'   => $dcpgettext,
                '_dcpf'  => $dcpfgettext,
                '_dcn'   => $dcngettext,
                '_dcnf'  => $dcnfgettext,
                '_dcnp'  => $dcnpgettext,
                '_dcnpf' => $dcnpfgettext
            );
        }
        
        return $functions;
    }
    
    public function fgettext($message) {
        $args = func_get_args();
        return vsprintf(gettext($message), array_slice($args, 1));
    }
    
    public function pgettext($context, $message) {
        return gettext($context . "\04" . $message);
    }
    
    public function pfgettext($context, $message) {
        $args = func_get_args();
        return vsprintf($this->pgettext($context, $message), array_slice($args, 2));
    }
    
    public function nfgettext($msgid1, $msgid2, $n) {
        $args = func_get_args();
        return vsprintf(ngettext($msgid1, $msgid2, $n), array_slice($args, 3));
    }
    
    public function npgettext($context, $msgid1, $msgid2, $n) {
        return ngettext($context . "\04" . $msgid1, $context . "\04" . $msgid2, $n);
    }
    
    public function npfgettext($context, $msgid1, $msgid2, $n) {
        $args = func_get_args();
        return vsprintf($this->npgettext($context, $msgid1, $msgid2, $n), array_slice($args, 4));
    }
    
    public function dfgettext($domain, $message) {
        $args = func_get_args();
        return vsprintf(dgettext($domain, $message), array_slice($args, 2));
    }
    
    public function dpgettext($context, $domain, $message) {
        return dgettext($domain, $context . "\04" . $message);
    }

    public function dpfgettext($context, $domain, $message) {
        $args = func_get_args();
        return vsprintf($this->dpgettext($context, $domain, $message), array_slice($args, 3));
    }
    
    public function dnfgettext($domain, $msgid1, $msgid2, $n) {
        $args = func_get_args();
        return vsprintf(dngettext($domain, $msgid1, $msgid2, $n), array_slice($args, 4));
    }

    public function dnpgettext($context, $domain, $msgid1, $msgid2, $n) {
        return dngettext($domain, $context . "\04" . $msgid1, $context . "\04" . $msgid2, $n);
    }

    public function dnpfgettext($context, $domain, $msgid1, $msgid2, $n) {
        $args = func_get_args();
        return vsprintf($this->dnpgettext($context, $domain, $msgid1, $msgid2, $n), array_slice($args, 5));
    }

    public function dcfgettext($domain, $message, $category) {
        $args = func_get_args();
        return vsprintf(dcgettext($domain, $message, $category), array_slice($args, 3));
    }
    
    public function dcpgettext($context, $domain, $message, $category) {
        return dcgettext($domain, $context . "\04" . $message, $category);
    }
    
    public function dcpfgettext($context, $domain, $message, $category) {
        $args = func_get_args();
        return vsprintf($this->dcpgettext($context, $domain, $message, $category), array_slice($args, 4));
    }
    
    public function dcnfgettext($domain, $msgid1, $msgid2, $n, $category) {
        $args = func_get_args();
        return vsprintf(dcngettext($domain, $msgid1, $msgid2, $n, $category), array_slice($args, 5));
    }
    
    public function dcnpgettext($context, $domain, $msgid1, $msgid2, $n, $category) {
        return dcngettext($domain, $context . "\04" . $msgid1, $context . "\04" . $msgid2, $n, $category);
    }
    
    public function dcnpfgettext($context, $domain, $msgid1, $msgid2, $n, $category) {
        $args = func_get_args();
        return vsprintf($this->dcnpgettext($context, $domain, $msgid1, $msgid2, $n, $category), array_slice($args, 6));
    }
    
}