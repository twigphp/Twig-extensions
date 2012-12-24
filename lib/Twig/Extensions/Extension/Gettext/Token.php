<?php

class Twig_Extensions_Extension_Gettext_Token extends Twig_Token {
    
    const COMMENT = 12;
    
    public static function typeToString($type, $short = false, $line = -1) {
        switch ($type) {
            case self::COMMENT :
                $name = 'COMMENT';
                break;
            default :
                return parent::typeToString($type, $short, $line);
        }

        return $short ? $name : 'Twig_Token::'.$name;
    }

    public static function typeToEnglish($type, $line = -1) {
        switch ($type) {
            case self::COMMENT :
                return 'comment';
            default :
                return parent::typeToEnglish($type, $line);
        }
    }

}