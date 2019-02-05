<?php

/*
 * This file is part of Twig.
 *
 * (c) 2010-2019 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Twig\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\Extensions\TokenParser\TransTokenParser;
use Twig\TwigFilter;

class I18nExtension extends AbstractExtension
{
    public function getTokenParsers()
    {
        return [new TransTokenParser()];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
             new TwigFilter('trans', 'gettext'),
        ];
    }
}
