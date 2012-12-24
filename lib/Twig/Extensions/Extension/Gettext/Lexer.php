<?php

class Twig_Extensions_Extension_Gettext_Lexer extends Twig_Lexer {
    
    protected function lexComment() {
        if (!preg_match($this->regexes['lex_comment'], $this->code, $match, PREG_OFFSET_CAPTURE, $this->cursor)) {
            throw new Twig_Error_Syntax('Unclosed comment', $this->lineno, $this->filename);
        }

        $value = substr($this->code, $this->cursor, $match[0][1] - $this->cursor);
        $this->pushToken(Twig_Extensions_Extension_Gettext_Token::COMMENT, $value);
        $this->moveCursor($value . $match[0][0]);
    }
    
    protected function pushToken($type, $value = '') {
        switch ($type) {
            case Twig_Extensions_Extension_Gettext_Token::COMMENT :
                $this->tokens[] = new Twig_Extensions_Extension_Gettext_Token($type, $value, $this->lineno);
                break;
            default :
                parent::pushToken($type, $value);
        }
    }
    
}