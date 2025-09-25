<?php

declare(strict_types=1);

namespace Stasis\Tests\Router;

use PHPUnit\Framework\TestCase;
use Stasis\Exception\LogicException;
use Stasis\Router\Compiler\CompiledRoute;
use Stasis\Router\Compiler\CompiledRouteCollection;
use Stasis\Router\Compiler\Resource\FileResource;
use Stasis\Router\RouteContainer;
use Stasis\Router\RouteData;
use Stasis\Router\Router;

class RouterTest extends TestCase
{
    private CompiledRouteCollection $routeCollection;
    private RouteContainer $routeContainer;
    private Router $router;

    public function setUp(): void
    {
        $this->routeCollection = new CompiledRouteCollection();
        $this->routeContainer = new RouteContainer();
        $this->router = new Router($this->routeCollection, $this->routeContainer);
    }

    public function testGetReturnsRouteDataByName(): void
    {
        $route = new CompiledRoute('/about', '/about/index.html', new FileResource('/src/about.md'), 'about');
        $this->routeCollection->add($route);

        $expected = new RouteData('/about', 'about');
        $actual = $this->router->get('about');

        self::assertEquals($expected, $actual, 'Unexpected route data returned');
    }

    public function testGetThrowsWhenRouteNotFound(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Route with name "missing" not found.');
        $this->router->get('missing');
    }

    public function testCurrentReturnsRouteData(): void
    {
        $current = new CompiledRoute('/home', '/index.html', new FileResource('/src/index.md'), 'home');
        $this->routeCollection->add($current);

        $this->routeContainer->route = $current;

        $expected = new RouteData('/home', 'home');
        $actual = $this->router->current();

        self::assertEquals($expected, $actual, 'Unexpected route data returned');
    }

    public function testCurrentThrowsWhenNotSet(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Current route is not set.');
        $this->router->current();
    }
}
