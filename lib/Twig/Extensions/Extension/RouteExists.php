<?php namespace 

AppBundle\Twig\Extension;

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

    public function getFilters()
    {
        return [
            new Twig_SimpleFilter('routeExists', [$this, 'routeExistsFilter']),
        ];
    }

    public function routeExistsFilter($route)
    {
        $router = $this->container->get('router');

        return (null === $router->getRouteCollection()->get($route)) ? false : true;
    }

    public function getName()
    {
        return 'app_route_exists_extension';
    }
}
