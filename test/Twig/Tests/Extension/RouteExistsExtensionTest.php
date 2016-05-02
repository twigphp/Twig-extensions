<?php namespace AppBundle\Tests\Twig\Extension;

use AppBundle\Twig\Extension\RouteExistsExtension;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RouteExistsExtensionTest extends WebTestCase
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    protected $client;

    /**
     * @var \AppBundle\Twig\Extension\RouteExistsExtension
     */
    protected $routeExistsExtension;

    /**
     * RouteExistsExtensionTest constructor
     *
     * Initialize a client so we can inject the container into the RouteExistsExtension
     */
    public function __construct()
    {
        parent::__construct();

        $this->client = static::createClient();

        $this->routeExistsExtension = new RouteExistsExtension($this->client->getContainer());
    }

    /**
     * Get sample data
     *
     * @return array
     */
    public function getRouteExistsExtensionTestData()
    {
        return [
            '_profiler_home'            => true,        // should always exist (if not, change to one that does)
            'this_route_does_not_exist' => false,       // should never exist (if you use this route, change the value)
        ];
    }

    /**
     * Filter test
     *
     * @param array|null $data
     * @param array|null $expectedResults
     */
    public function testRouteExistsExtension(array $data = null, array $expectedResults = null)
    {
        $dataCount = count($data);

        for ($i = 0; $i < $dataCount; $i++) {
            if($expectedResults[$i] === false){
                $this->assertFalse($this->routeExistsExtension->routeExistsFilter($data[$i]));
            }elseif ($expectedResults[$i] === true){
                $this->assertTrue($this->routeExistsExtension->routeExistsFilter($data[$i]));
            }
        }
    }

    /**
     * An empty string should result in FALSE
     */
    public function testRouteExistsExtensionOnEmptyString()
    {
        $this->assertFalse($this->routeExistsExtension->routeExistsFilter(''));
    }
}
