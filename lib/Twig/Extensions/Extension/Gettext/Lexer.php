<?php

class Twig_Extensions_Extension_Gettext_Lexer extends Twig_Lexer {
    
    protected $commentTokens = array();
    
    /**
     * Overrides tokenize to initialize $this->commentTokens.
     */
    public function tokenize($code, $filename = null) {
        $this->commentTokens = array();
        return parent::tokenize($code, $filename);
    }
    
    /**
     * Overrides lexComment by saving comment tokens into $this->commentTokens
     * instead of just ignoring them.
     */
    protected function lexComment() {
        if (!preg_match($this->regexes['lex_comment'], $this->code, $match, PREG_OFFSET_CAPTURE, $this->cursor)) {
            throw new Twig_Error_Syntax('Unclosed comment', $this->lineno, $this->filename);
        }

        $value = substr($this->code, $this->cursor, $match[0][1] - $this->cursor);
        $token = new Twig_Extensions_Extension_Gettext_Token(Twig_Extensions_Extension_Gettext_Token::COMMENT, $value, $this->lineno);
        $this->commentTokens[] = $token;
        $this->moveCursor($value . $match[0][0]);
    }
    
    /**
     * Returns the comment tokens that were extracted.
     */
    public function getCommentTokens() {
        return $this->commentTokens;
    }
    
}