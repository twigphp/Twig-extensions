<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (!defined('TWIG_LIB_DIR') || 'NOT_SET' === TWIG_LIB_DIR) {
    die('The path to the Twig lib/ directory must be defined in phpunit.xml configuration.');
}

require_once TWIG_LIB_DIR.'/Twig/Autoloader.php';
Twig_Autoloader::register();

function twigExtensionAutoload($class)
{
    if (0 !== strpos($class, 'Twig_Extensions')) {
        return;
    }

    if (file_exists($file = dirname(__FILE__).'/../lib/'.str_replace('_', '/', $class).'.php')) {
        require $file;
    }
}

spl_autoload_register('twigExtensionAutoload');
