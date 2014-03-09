<?php

/**
 * @author Claudio Mulas <claudio.mulas@lucla.net>
 */
 
namespace Admin\DashboardBundle\Twig;

class Twig_Extensions_Extension_Gravatar extends Twig_Extension
{
    /**
     * Returns a list of filters.
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter('gravatar', array($this, 'gravatar')),
        );
    }

    public function gravatar($email, $size = 80, $default = 0)
    {
        $grav_url = "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $email ) ) ) . "?d=" . urlencode( $default ) . "&s=" . $size;

        return $grav_url;
    }

    public function getName()
    {
        return 'gravatar';
    }
}
