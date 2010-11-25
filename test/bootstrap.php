<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (!defined('TWIG_LIBRARY_PATH')) {
    die('path to twig library must be defined in phpunit.xml configuration');
}

require_once TWIG_LIBRARY_PATH.'/Twig/Autoloader.php';
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