<?php

class Twig_Extensions_Extension_Gettext_Extractor {
    
    protected $strings = array();
    
    protected $comments = array();
    
    protected $file;
    
    protected $lexer;
    
    protected $parser;
    
    protected $POStringFactory;
    
    public function __construct(Twig_Extensions_Extension_Gettext_POString_Factory_Interface $POStringFactory) {
        $this->POStringFactory = $POStringFactory;
        
        $twig         = new Twig_Environment(new Twig_Loader_String);
        $this->lexer  = new Twig_Extensions_Extension_Gettext_Lexer($twig);
        $this->parser = new Twig_Parser($twig);
    }
    
    public function extractFile($file) {
        $this->strings = array();
        $this->file    = $file;
        
        $source = file_get_contents($file);
        $tokens = $this->lexer->tokenize($source);
        $tokens = $this->extractComments($tokens);
        $node   = $this->parser->parse($tokens);
        $this->processNode($node);
        
        return $this->strings;
    }
    
    protected function extractComments(Twig_TokenStream $stream) {
        $tokens = array();
        
        while (!$stream->isEOF()) {
            $token = $stream->next();
            
            switch ($token->getType()) {
                case Twig_Extensions_Extension_Gettext_Token::COMMENT :
                    $this->comments[] = $token;
                    break;
                default :
                    $tokens[] = $token;
            }
        }
        
        $tokens[] = $stream->getCurrent();
        
        return new Twig_TokenStream($tokens, $stream->getFilename());
    }
    
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
    
    protected function processNode(Twig_NodeInterface $node) {
        if ($node instanceof Twig_Node_Expression_Function) {
            $this->processFunctionNode($node);
        }
    
        foreach ($node as $child) {
            if ($child instanceof Twig_NodeInterface) {
                $this->processNode($child);
            }
        }
    }
    
    protected function processFunctionNode(Twig_Node_Expression_Function $node) {
        switch ($node->getAttribute('name')) {
            case '_' :
            case 'gettext' :
            case '_f' :
            case 'fgettext' :
                $this->gettext($node);
                break;
            case '_p' :
            case 'pgettext' :
            case '_pf' :
            case 'pfgettext' :
                $this->pgettext($node);
                break;
            case '_n' :
            case 'ngettext' :
            case '_nf' :
            case 'nfgettext' :
                $this->ngettext($node);
                break;
            case '_np' :
            case 'npgettext' :
            case '_npf' :
            case 'npfgettext' :
                $this->npgettext($node);
                break;
            case '_d' :
            case 'dgettext' :
            case '_df' :
            case 'dfgettext' :
                $this->dgettext($node);
                break;
            case '_dp' :
            case 'dpgettext' :
            case '_dpf' :
            case 'dpfgettext' :
                $this->dpgettext($node);
                break;
            case '_dn' :
            case 'dngettext' :
            case '_dnf' :
            case 'dnfgettext' :
                $this->dngettext($node);
                break;
            case '_dnp' :
            case 'dnpgettext' :
            case '_dnpf' :
            case 'dnpfgettext' :
                $this->dnpgettext($node);
                break;
            case '_dc' :
            case 'dcgettext' :
            case '_dcf' :
            case 'dcfgettext' :
                $this->dcgettext($node);
                break;
            case '_dcp' :
            case 'dcpgettext' :
            case '_dcpf' :
            case 'dcpfgettext' :
                $this->dcpgettext($node);
                break;
            case '_dcn' :
            case 'dcngettext' :
            case '_dcnf' :
            case 'dcnfgettext' :
                $this->dcngettext($node);
                break;
            case '_dcnp' :
            case 'dcnpgettext' :
            case '_dcnpf' :
            case 'dcnpfgettext' :
                $this->dcnpgettext($node);
                break;
        }
    }
    
    protected function gettext(Twig_Node_Expression_Function $node) {
        $arguments = $this->validateArguments($node, 1, 'Twig_Node_Expression_Constant');
        $this->pushEntry($this->getPOString($arguments[0]->getAttribute('value')), $node->getLine());
    }
    
    protected function pgettext(Twig_Node_Expression_Function $node) {
        $arguments = $this->validateArguments($node, 2, 'Twig_Node_Expression_Constant', 'Twig_Node_Expression_Constant');
        $string = $this->getPOString($arguments[1]->getAttribute('value'));
        $string->setMsgctxt($arguments[0]->getAttribute('value'));
        $this->pushEntry($string, $node->getLine());
    }
        
    protected function ngettext(Twig_Node_Expression_Function $node) {
        $arguments = $this->validateArguments($node, 3, 'Twig_Node_Expression_Constant', 'Twig_Node_Expression_Constant', null);
        $string = $this->getPOString($arguments[0]->getAttribute('value'), $arguments[1]->getAttribute('value'));
        $this->pushEntry($string, $node->getLine());
    }
        
    protected function npgettext(Twig_Node_Expression_Function $node) {
        $arguments = $this->validateArguments($node, 4, 'Twig_Node_Expression_Constant', 'Twig_Node_Expression_Constant', 'Twig_Node_Expression_Constant', null);
        $string = $this->getPOString($arguments[1]->getAttribute('value'), $arguments[2]->getAttribute('value'));
        $string->setMsgctxt($arguments[0]->getAttribute('value'));
        $this->pushEntry($string, $node->getLine());
    }
        
    protected function dgettext(Twig_Node_Expression_Function $node) {
        $arguments = $this->validateArguments($node, 2, 'Twig_Node_Expression_Constant', 'Twig_Node_Expression_Constant');
        $string = $this->getPOString($arguments[1]->getAttribute('value'));
        $string->setDomain($arguments[0]->getAttribute('value'));
        $this->pushEntry($string, $node->getLine());
    }
        
    protected function dpgettext(Twig_Node_Expression_Function $node) {
        $arguments = $this->validateArguments($node, 3, 'Twig_Node_Expression_Constant', 'Twig_Node_Expression_Constant', 'Twig_Node_Expression_Constant');
        $string = $this->getPOString($arguments[2]->getAttribute('value'));
        $string->setMsgctxt($arguments[1]->getAttribute('value'));
        $string->setDomain($arguments[0]->getAttribute('value'));
        $this->pushEntry($string, $node->getLine());
    }
        
    protected function dngettext(Twig_Node_Expression_Function $node) {
        $arguments = $this->validateArguments($node, 4, 'Twig_Node_Expression_Constant', 'Twig_Node_Expression_Constant', 'Twig_Node_Expression_Constant', null);
        $string = $this->getPOString($arguments[1]->getAttribute('value'), $arguments[2]->getAttribute('value'));
        $string->setDomain($arguments[0]->getAttribute('value'));
        $this->pushEntry($string, $node->getLine());
    }
    
    protected function dnpgettext(Twig_Node_Expression_Function $node) {
        $arguments = $this->validateArguments($node, 5, 'Twig_Node_Expression_Constant', 'Twig_Node_Expression_Constant', 'Twig_Node_Expression_Constant', 'Twig_Node_Expression_Constant', null);
        $string = $this->getPOString($arguments[2]->getAttribute('value'), $arguments[3]->getAttribute('value'));
        $string->setDomain($arguments[0]->getAttribute('value'));
        $string->setMsgctxt($arguments[1]->getAttribute('value'));
        $this->pushEntry($string, $node->getLine());
    }
    
    protected function dcgettext(Twig_Node_Expression_Function $node) {
        $arguments = $this->validateArguments($node, 3, 'Twig_Node_Expression_Constant', 'Twig_Node_Expression_Constant', 'Twig_Node_Expression_Constant');
        $string = $this->getPOString($arguments[1]->getAttribute('value'));
        $string->setDomain($arguments[0]->getAttribute('value'));
        $string->setCategory(constant($arguments[2]->getAttribute('value')));
        $this->pushEntry($string, $node->getLine());
    }
    
    protected function dcpgettext(Twig_Node_Expression_Function $node) {
        $arguments = $this->validateArguments($node, 4, 'Twig_Node_Expression_Constant', 'Twig_Node_Expression_Constant', 'Twig_Node_Expression_Constant', 'Twig_Node_Expression_Constant');
        $string = $this->getPOString($arguments[1]->getAttribute('value'));
        $string->setDomain($arguments[0]->getAttribute('value'));
        $string->setMsgctxt($arguments[1]->getAttribute('value'));
        $string->setCategory(constant($arguments[3]->getAttribute('value')));
        $this->pushEntry($string, $node->getLine());
    }
    
    protected function dcngettext(Twig_Node_Expression_Function $node) {
        $arguments = $this->validateArguments($node, 5, 'Twig_Node_Expression_Constant', 'Twig_Node_Expression_Constant', 'Twig_Node_Expression_Constant', null, 'Twig_Node_Expression_Constant');
        $string = $this->getPOString($arguments[1]->getAttribute('value'), $arguments[2]->getAttribute('value'));
        $string->setDomain($arguments[0]->getAttribute('value'));
        $string->setCategory(constant($arguments[4]->getAttribute('value')));
        $this->pushEntry($string, $node->getLine());
    }
    
    protected function dcnpgettext(Twig_Node_Expression_Function $node) {
        $arguments = $this->validateArguments($node, 6, 'Twig_Node_Expression_Constant', 'Twig_Node_Expression_Constant', 'Twig_Node_Expression_Constant', 'Twig_Node_Expression_Constant', null, 'Twig_Node_Expression_Constant');
        $string = $this->getPOString($arguments[2]->getAttribute('value'), $arguments[3]->getAttribute('value'));
        $string->setDomain($arguments[0]->getAttribute('value'));
        $string->setMsgctxt($arguments[1]->getAttribute('value'));
        $string->setCategory(constant($arguments[5]->getAttribute('value')));
        $this->pushEntry($string, $node->getLine());
    }
        
    protected function getArguments(Twig_Node_Expression_Function $functionNode) {
        $arguments = array();
        foreach ($functionNode->getNode('arguments') as $argument) {
            $arguments[] = $argument;
        }
        return $arguments;
    }
    
    protected function validateArguments(Twig_Node_Expression_Function $functionNode, $minNum /*, type [, type... ] */) {
        $arguments = $this->getArguments($functionNode);
        
        if (count($arguments) < $minNum) {
            throw new InvalidArgumentException(sprintf('Function %s expects at least %d arguments, found %d in %s on line %d',
                                                       $functionNode->getAttribute('name'),
                                                       $minNum,
                                                       count($arguments),
                                                       $this->file,
                                                       $functionNode->getLine()));
        }
        
        $types = func_get_args();
        $types = array_slice($types, 2);
        
        if (count($types) < $minNum) {
            throw new LogicException(sprintf('Minimum number of arguments given as %d, but only %d type validations supplied',
                                             $minNum, count($types)));
        }
        
        for ($i = 0; $i < $minNum; $i++) {
            if ($types[$i] === null) {
                continue;
            }
            if (!($arguments[$i] instanceof $types[$i])) {
                throw new InvalidArgumentException(sprintf('Argument %d for %s must be of type %s, found %s in %s on line %d',
                                                           $i + 1,
                                                           $functionNode->getAttribute('name'),
                                                           $types[$i],
                                                           get_class($arguments[$i]),
                                                           $this->file,
                                                           $functionNode->getLine()));
            }
        }
        
        return $arguments;
    }
    
    protected function pushEntry(Twig_Extensions_Extension_Gettext_POString_Interface $string, $line) {
        if ($comment = $this->getPreceedingCommentNode($line)) {
            $string->setExtractedComments(trim($comment->getValue()));
        }
        $string->addReference("$this->file:$line");
        $this->strings[] = $string;
    }
    
    protected function getPOString($msgid, $msgidPlural = null) {
        return $this->POStringFactory->construct($msgid, $msgidPlural);
    }
    
}