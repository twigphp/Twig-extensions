<?php

class Twig_Extensions_Extension_Gettext_Extractor {
    
    const VARIABLE     = 0;
    const MSGID        = 'msgid';
    const MSGID_PLURAL = 'msgid_plural';
    const DOMAIN       = 'domain';
    const CATEGORY     = 'category';
    const CONTEXT      = 'context';
    
    protected $strings = array();
    
    protected $comments = array();
    
    protected $file;
    
    protected $lexer;
    
    protected $parser;
    
    protected $POStringFactory;
    
    protected $inFormatFilterContext = false;
    
    /**
     * @param Twig_Extensions_Extension_Gettext_POString_Factory_Interface $POStringFactory An object for constructing POString objects.
     * @param array $extensions Additional extensions that need to be loaded into the environment.
     */
    public function __construct(Twig_Extensions_Extension_Gettext_POString_Factory_Interface $POStringFactory, array $extensions = array()) {
        $this->POStringFactory = $POStringFactory;
        
        $twig = new Twig_Environment(new Twig_Loader_String);
        array_map(array($twig, 'addExtension'), $extensions);
        
        $this->lexer  = new Twig_Extensions_Extension_Gettext_Lexer($twig);
        $this->parser = new Twig_Parser($twig);
    }
    
    /**
     * Extracts all gettext strings from given Twig template file.
     * 
     * @param string $file Path to Twig template file.
     * @return array Array of POString objects.
     */
    public function extractFile($file) {
        $this->strings = array();
        $this->file    = $file;
        
        $source = file_get_contents($file);
        $tokens = $this->lexer->tokenize($source);
        $this->comments = $this->lexer->getCommentTokens();
        $node = $this->parser->parse($tokens);
        $this->processNode($node);
        
        return $this->strings;
    }
    
    /**
     * Returns comment node immediately preceeding given line number, if any.
     * 
     * @param int $lineno
     * @return Twig_Extensions_Extension_Gettext_Token Closest preceeding comment token or null.
     */
    protected function getPreceedingCommentNode($lineno) {
        $commentNode = null;
        foreach ($this->comments as $comment) {
            if ($comment->getLine() <= $lineno) {
                $commentNode = $comment;
            } else {
                break;
            }
        }
        if (!$commentNode) {
            return;
        }
        
        $lines = substr_count($commentNode->getValue(), "\n") + 1;
        if ($commentNode->getLine() + $lines !=  $lineno) {
            return;
        }
        
        return $commentNode;
    }
    
    /**
     * Processes a node and its child nodes recursively.
     * 
     * @param Twig_NodeInterface $node
     * @param bool $inFormatFilterContext Set to true if we're descending into a `format` filter, parameter is passed down recursively.
     */
    protected function processNode(Twig_NodeInterface $node, $inFormatFilterContext = false) {
        $this->inFormatFilterContext = $inFormatFilterContext;
        
        switch (true) {
            case $node instanceof Twig_Node_Expression_Function :
                $this->processFunctionNode($node);
                break;
            case $node instanceof Twig_Node_Expression_Filter :
                $this->processFilterNode($node);
                break;
        }
    
        foreach ($node as $child) {
            if ($child instanceof Twig_NodeInterface) {
                $this->processNode($child, $inFormatFilterContext || $this->isFormatFilter($node));
            }
        }
    }
    
    /**
     * @param Twig_NodeInterface $node The node to be checked.
     * @return bool True if $node is a `format` filter.
     */
    protected function isFormatFilter(Twig_NodeInterface $node) {
        return $node instanceof Twig_Node_Expression_Filter && $node->getNode('filter')->getAttribute('value') == 'format';
    }
    
    /**
     * Processes a Function node.
     * 
     * @param Twig_Node_Expression_Function $node
     */
    protected function processFunctionNode(Twig_Node_Expression_Function $node) {
        switch ($node->getAttribute('name')) {
            case '_' :
            case 'gettext' :
                $this->pushFunction($node, self::MSGID);
                break;
            case '_p' :
            case 'pgettext' :
                $this->pushFunction($node, self::CONTEXT, self::MSGID);
                break;
            case '_n' :
            case 'ngettext' :
                $this->pushFunction($node, self::MSGID, self::MSGID_PLURAL, self::VARIABLE);
                break;
            case '_np' :
            case 'npgettext' :
                $this->pushFunction($node, self::CONTEXT, self::MSGID, self::MSGID_PLURAL, self::VARIABLE);
                break;
            case '_d' :
            case 'dgettext' :
                $this->pushFunction($node, self::DOMAIN, self::MSGID);
                break;
            case '_dp' :
            case 'dpgettext' :
                $this->pushFunction($node, self::CONTEXT, self::DOMAIN, self::MSGID);
                break;
            case '_dn' :
            case 'dngettext' :
                $this->pushFunction($node, self::DOMAIN, self::MSGID, self::MSGID_PLURAL, self::VARIABLE);
                break;
            case '_dnp' :
            case 'dnpgettext' :
                $this->pushFunction($node, self::CONTEXT, self::DOMAIN, self::MSGID, self::MSGID_PLURAL, self::VARIABLE);
                break;
            case '_dc' :
            case 'dcgettext' :
                $this->pushFunction($node, self::DOMAIN, self::MSGID, self::CATEGORY);
                break;
            case '_dcp' :
            case 'dcpgettext' :
                $this->pushFunction($node, self::CONTEXT, self::DOMAIN, self::MSGID, self::CATEGORY);
                break;
            case '_dcn' :
            case 'dcngettext' :
                $this->pushFunction($node, self::DOMAIN, self::MSGID, self::MSGID_PLURAL, self::VARIABLE, self::CATEGORY);
                break;
            case '_dcnp' :
            case 'dcnpgettext' :
                $this->pushFunction($node, self::CONTEXT, self::DOMAIN, self::MSGID, self::MSGID_PLURAL, self::VARIABLE, self::CATEGORY);
                break;
        }
    }
    
    /**
     * Processes a Filter node.
     * 
     * @param Twig_Node_Expression_Filter $node
     */
    protected function processFilterNode(Twig_Node_Expression_Filter $node) {
        switch ($node->getNode('filter')->getAttribute('value')) {
            case '_' :
            case 'gettext' :
                $this->pushFilter($node);
                break;
            case '_p' :
            case 'pgettext' :
                $this->pushFilter($node, self::CONTEXT);
                break;
            case '_n' :
            case 'ngettext' :
                $this->pushFilter($node, self::MSGID_PLURAL);
                break;
            case '_np' :
            case 'npgettext' :
                $this->pushFilter($node, self::MSGID_PLURAL, self::VARIABLE, self::CONTEXT);
                break;
            case '_d' :
            case 'dgettext' :
                $this->pushFilter($node, self::DOMAIN);
                break;
            case '_dp' :
            case 'dpgettext' :
                $this->pushFilter($node, self::DOMAIN, self::CONTEXT);
                break;
            case '_dn' :
            case 'dngettext' :
                $this->pushFilter($node, self::MSGID_PLURAL, self::VARIABLE, self::DOMAIN);
                break;
            case '_dnp' :
            case 'dnpgettext' :
                $this->pushFilter($node, self::MSGID_PLURAL, self::VARIABLE, self::DOMAIN, self::CONTEXT);
                break;
            case '_dc' :
            case 'dcgettext' :
                $this->pushFilter($node, self::DOMAIN, self::CATEGORY);
                break;
            case '_dcp' :
            case 'dcpgettext' :
                $this->pushFilter($node, self::DOMAIN, self::CATEGORY, self::CONTEXT);
                break;
            case '_dcn' :
            case 'dcngettext' :
                $this->pushFilter($node, self::MSGID_PLURAL, self::VARIABLE, self::DOMAIN, self::CATEGORY);
                break;
            case '_dcnp' :
            case 'dcnpgettext' :
                $this->pushFilter($node, self::MSGID_PLURAL, self::VARIABLE, self::DOMAIN, self::CATEGORY, self::CONTEXT);
                break;
        }
    }
    
    /**
     * Parses arguments of a Function node and pushes entry into list of extracted strings.
     * 
     * @param Twig_Node_Expression_Function $node
     * @param mixed Variable number of arguments representing roles of the Twig function arguments.
     */
    protected function pushFunction(Twig_Node_Expression_Function $node /*, arg, .. */) {
        $args = func_get_args();
        array_shift($args);

        $valueNodes = array();
        
        foreach ($node->getNode('arguments') as $i => $argumentNode) {
            if (!isset($args[$i])) {
                break;
            }
            $valueNodes[$args[$i]] = $argumentNode;
        }
        
        $this->pushEntry($node, $valueNodes);
    }
    
    /**
     * Parses arguments of a Filter node and pushes entry into list of extracted strings.
     * 
     * @param Twig_Node_Expression_Filter $node
     * @param mixed Variable number of arguments representing roles of the Twig filter arguments.
     */
    protected function pushFilter(Twig_Node_Expression_Filter $node /*, arg, .. */) {
        $args = func_get_args();
        array_shift($args);
        
        $valueNodes = array(self::MSGID => $node->getNode('node'));
        
        foreach ($node->getNode('arguments') as $i => $argumentNode) {
            if (!isset($args[$i])) {
                break;
            }
            $valueNodes[$args[$i]] = $argumentNode;
        }
        
        $this->pushEntry($node, $valueNodes);
    }
    
    /**
     * Pushes entry into list of extracted strings.
     * 
     * @param Twig_Node_Expression $node
     * @param array $valueNodes Associative array of value nodes (values) and their role (keys).
     */
    protected function pushEntry(Twig_Node_Expression $node, array $valueNodes) {
        if (!isset($valueNodes[self::MSGID])) {
            throw new LogicException('$valueNodes array must contain a MSGID value');
        }
        
        $POString = $this->POStringFactory->construct($valueNodes[self::MSGID]->getAttribute('value'));
        
        foreach ($valueNodes as $type => $argument) {
            if ($type === self::VARIABLE) {
                continue;
            } else if (!($argument instanceof Twig_Node_Expression_Constant)) {
                $this->invalidArgumentTypeParseError($argument, $node);
            }
            
            switch ($type) {
                case self::MSGID :
                    continue;
                case self::MSGID_PLURAL :
                    $POString->setMsgidPlural($argument->getAttribute('value'));
                    break;
                case self::DOMAIN :
                    $POString->setDomain($argument->getAttribute('value'));
                    break;
                case self::CATEGORY :
                    $POString->setCategory($argument->getAttribute('value'));
                    break;
                case self::CONTEXT :
                    $POString->setMsgctxt($argument->getAttribute('value'));
                    break;
                default :
                    throw new InvalidArgumentException("Invalid argument '$type'");
            }
        }
        
        if ($comment = $this->getPreceedingCommentNode($node->getLine())) {
            $POString->addExtractedComment(trim($comment->getValue()));
        }

        $POString->addReference(sprintf("$this->file:%d", $node->getLine()));

        if ($this->inFormatFilterContext) {
            $POString->addFlag('php-format');
        }

        $this->strings[] = $POString;
    }
    
    /**
     * Throws an exception with detailled error message in case of argument parse errors.
     * 
     * @param Twig_Node_Expression $argument The invalid/unexpected argument node.
     * @param Twig_Node_Expression $node The Filter or Function node to which the $argument belongs.
     * @throws InvalidArgumentException (The main purpose.)
     * @throws LogicException In case an unexpected $node argument was passed.
     */
    protected function invalidArgumentTypeParseError(Twig_Node_Expression $argument, Twig_Node_Expression $node) {
        switch (true) {
            case $node instanceof Twig_Node_Expression_Function :
                $name = $node->getAttribute('name');
                break;
            case $node instanceof Twig_Node_Expression_Filter :
                $name = $node->getNode('filter')->getAttribute('value');
                break;
            default :
                throw new LogicException(sprintf("Don't know how to get name of node %s to throw an InvalidArgumentException",
                                                 get_class($node)));
        }
        
        throw new InvalidArgumentException(sprintf('Invalid argument of type %s for %s in %s on line %d',
                                                   get_class($argument), $name, $this->file, $node->getLine()));
    }
    
}