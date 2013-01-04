<?php

interface Twig_Extensions_Extension_Gettext_POString_Interface {
    
    /**
     * @param string $msgid The primary localizable string.
     */
    public function __construct($msgid);

    /**
     * @param string $msgidPlural The pluralized string (for ngettext and similar).
     */
    public function setMsgidPlural($msgidPlural);

    /**
     * @param int $category The category, i.e. one of the LC_* constants.
     */
    public function setCategory($category);
    
    /**
     * @param string $domain The domain (for dgettext and similar).
     */
    public function setDomain($domain);
    
    /**
     * @param string $msgctxt The context (for pgettext and similar).
     */
    public function setMsgctxt($msgctxt);
    
    /**
     * Add a related source code comment. There may be more than one.
     * 
     * @param string $comment Arbitrary text. May contain xgettext flags, which this method may parse.
     */
    public function addExtractedComment($comment);
    
    /**
     * Add a reference to a source code file and line number.
     * There may be more than one.
     * 
     * @param string $reference Single line strings like "/path/to/file.twig:42"
     */
    public function addReference($reference);
    
    /**
     * Add a flag. There may be more than one. The object should take care that mutually exclusive
     * flags are treated as such, typically by unsetting previously set conflicting flags.
     * 
     * @param string $flag Flags like "php-format", "no-php-format", "range: 0..42" etc.
     */
    public function addFlag($flag);
    
}