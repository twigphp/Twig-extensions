<?php

/**
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Alejandro Hurtado <ahurt2000@gmail.com> <alejandro.hurtado@awebzone.com>
 * @package Twig
 * @subpackage Twig-extensions
 */
class Twig_Extensions_Extension_Addslashes extends Twig_Extension {

    public function getFilters() {
        return array(
            'addslashes' => new Twig_Filter_Function('twig_addslashes'),
        );
    }

    public function getName() {
        return 'Addslashes';
    }

}

function twig_addslashes($value) {
    return addslashes($value);
}

