<?php

interface Twig_Extensions_Extension_Gettext_POString_Interface {
    
    public function __construct($msgid);

    public function setMsgidPlural($msgidPlural);

    public function setCategory($category);
    
    public function setDomain($domain);
    
    public function setMsgctxt($msgctxt);
    
    public function addExtractedComment($comment);
    
    public function addReference($reference);
    
    public function addFlag($flag);
    
}