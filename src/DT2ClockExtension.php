<?php

namespace Twig\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class DT2ClockExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('dt2clock', [$this, 'formatDateTime']),
        ];
    }

    public function formatDateTime(\DateTimeInterface $datetime)
    {
        $clocks = [
            "🕐",  // U+1F550  CLOCK FACE ONE OCLOCK
            "🕜",  // U+1F55C  CLOCK FACE ONE-THIRTY
            "🕑",  // U+1F551  CLOCK FACE TWO OCLOCK
            "🕝",  // U+1F55D  CLOCK FACE TWO-THIRTY
            "🕒",  // U+1F552  CLOCK FACE THREE OCLOCK
            "🕞",  // U+1F55E  CLOCK FACE THREE-THIRTY
            "🕓",  // U+1F553  CLOCK FACE FOUR OCLOCK
            "🕟",  // U+1F55F  CLOCK FACE FOUR-THIRTY
            "🕔",  // U+1F554  CLOCK FACE FIVE OCLOCK
            "🕠",  // U+1F560  CLOCK FACE FIVE-THIRTY
            "🕕",  // U+1F555  CLOCK FACE SIX OCLOCK
            "🕡",  // U+1F561  CLOCK FACE SIX-THIRTY
            "🕖",  // U+1F556  CLOCK FACE SEVEN OCLOCK
            "🕢",  // U+1F562  CLOCK FACE SEVEN-THIRTY
            "🕗",  // U+1F557  CLOCK FACE EIGHT OCLOCK
            "🕣",  // U+1F563  CLOCK FACE EIGHT-THIRTY
            "🕘",  // U+1F558  CLOCK FACE NINE OCLOCK
            "🕤",  // U+1F564  CLOCK FACE NINE-THIRTY
            "🕙",  // U+1F559  CLOCK FACE TEN OCLOCK
            "🕥",  // U+1F565  CLOCK FACE TEN-THIRTY
            "🕚",  // U+1F55A  CLOCK FACE ELEVEN OCLOCK
            "🕦",  // U+1F566  CLOCK FACE ELEVEN-THIRTY
            "🕛",  // U+1F55B  CLOCK FACE TWELVE OCLOCK
            "🕧",  // U+1F567  CLOCK FACE TWELVE-THIRTY
        ];

        $hours = (float) $datetime->format('g');
        $minutes = (float) $datetime->format('i');

        $decimalHour = $hours + ($minutes / 60);

        $clockIndex = (int) round($decimalHour * 2) - 2;
        
        return $clocks[$clockIndex];
    }
}
