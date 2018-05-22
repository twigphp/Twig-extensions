<?php

/**
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Jean85\PrettyVersions;
use PackageVersions\Versions;

/**
 * @author Edi Modrić <edi.modric@gmail.com>
 */
class Twig_Extensions_Extension_Version extends Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter('package_version', 'twig_package_version'),
            new Twig_SimpleFilter('pretty_package_version', 'twig_pretty_package_version'),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Version';
    }
}

function twig_package_version($value)
{
    if (!class_exists('PackageVersions\Versions')) {
        throw new RuntimeException('ocramius/package-versions library is needed to use package_version Twig function.');
    }

    return Versions::getVersion($value);
}

function twig_pretty_package_version($value)
{
    if (!class_exists('Jean85\PrettyVersions')) {
        throw new RuntimeException('jean85/pretty-package-versions library is needed to use pretty_package_version Twig function.');
    }

    return PrettyVersions::getVersion($value);
}

class_alias('Twig_Extensions_Extension_Version', 'Twig\Extensions\VersionExtension', false);
