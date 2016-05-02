<?php

namespace AppBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\Container;
use Twig_Extension;
use Twig_SimpleFilter;

class RouteExistsExtension extends Twig_Extension
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc} 
     */
    public function getFilters()
    {
        return [
            new Twig_SimpleFilter('routeExists', [$this, 'routeExistsFilter']),
        ];
    }

    /**
     * Determine whether a route exists. If no value is passed ($route is empty()), return FALSE.
     *
     * @param $route
     *
     * @return bool
     * @throws \Exception
     */
    public function routeExistsFilter($route)
    {
        $router = $this->container->get('router');

        if(empty($route)){
            return false;
        }

        return (null === $router->getRouteCollection()->get($route)) ? false : true;
    }

    /**
     * {@inheritdoc} 
     */
    public function getName()
    {
        return 'app_route_exists_extension';
    }
}
