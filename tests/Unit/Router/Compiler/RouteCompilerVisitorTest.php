<?php

declare(strict_types=1);

namespace Stasis\Tests\Unit\Router\Compiler;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Stasis\Router\Compiler\CompiledRoute;
use Stasis\Router\Compiler\Resource\ControllerResource;
use Stasis\Router\Compiler\Resource\FileResource;
use Stasis\Router\Compiler\RouteCompilerVisitor;
use Stasis\Router\Route\Asset;
use Stasis\Router\Route\Group;
use Stasis\Router\Route\Route;
use Stasis\Router\Route\RouteProviderInterface;
use Stasis\ServiceLocator\ServiceLocator;

class RouteCompilerVisitorTest extends TestCase
{
    private MockObject&ServiceLocator $serviceLocator;
    private RouteCompilerVisitor $visitor;

    public function setUp(): void
    {
        $this->serviceLocator = $this->createMock(ServiceLocator::class);
        $this->visitor = new RouteCompilerVisitor('/base', $this->serviceLocator);
    }

    #[DataProvider('visitRouteProvider')]
    public function testVisitRoute(string $path, string $expectedPath, string $expectedDist): void
    {
        $controller = static fn() => 'OK';
        $route = new Route($path, $controller, 'example_name', ['a' => 1]);

        $this->serviceLocator
            ->expects($this->never())
            ->method('get');

        $expected = new CompiledRoute(
            $expectedPath,
            $expectedDist,
            new ControllerResource($controller, ['a' => 1]),
            'example_name',
        );

        $this->visitor->visitRoute($route);
        $actual = iterator_to_array($this->visitor->routes->all());
        self::assertEquals([$expected], $actual, 'Unexpected route compilation result');
    }

    public static function visitRouteProvider(): array
    {
        return [
            'directory' => ['/blog', '/base/blog', '/base/blog/index.html'],
            'file' => ['/sitemap.xml', '/base/sitemap.xml', '/base/sitemap.xml'],
        ];
    }

    public function testVisitAsset(): void
    {
        $asset = new Asset('/images/logo.png', '/src/assets/logo.png', 'logo');

        $this->serviceLocator
            ->expects($this->never())
            ->method('get');

        $expected = new CompiledRoute(
            '/base/images/logo.png',
            '/base/images/logo.png',
            new FileResource('/src/assets/logo.png'),
            'logo',
        );

        $this->visitor->visitAsset($asset);
        $actual = iterator_to_array($this->visitor->routes->all());
        self::assertEquals([$expected], $actual, 'Unexpected route compilation result');
    }

    public function testVisitGroupWithIterable(): void
    {
        $controller = static fn() => 'OK';

        $group = new Group('/blog', [
            new Route('/post', $controller, 'post'),
            new Asset('/assets/style.css', '/src/style.css', 'style'),
        ]);

        $this->serviceLocator
            ->expects($this->never())
            ->method('get');

        $expectedRoute = new CompiledRoute(
            '/base/blog/post',
            '/base/blog/post/index.html',
            new ControllerResource($controller, []),
            'post',
        );
        $expectedAsset = new CompiledRoute(
            '/base/blog/assets/style.css',
            '/base/blog/assets/style.css',
            new FileResource('/src/style.css'),
            'style',
        );

        $this->visitor->visitGroup($group);

        $actual = iterator_to_array($this->visitor->routes->all());
        self::assertEquals([$expectedRoute, $expectedAsset], $actual, 'Unexpected route compilation result');
    }

    public function testVisitGroupWithProviderInstance(): void
    {
        $controller1 = static fn() => 'A';
        $route1 = new Route('/a', $controller1, 'a');

        $controller2 = static fn() => 'B';
        $route2 = new Route('/b', $controller2, 'b');

        $provider = new class ($route1, $route2) implements RouteProviderInterface {
            public function __construct(
                private Route $route1,
                private Route $route2,
            ) {}

            public function routes(): iterable
            {
                yield $this->route1;
                yield $this->route2;
            }
        };

        $this->serviceLocator
            ->expects($this->never())
            ->method('get');

        $expected1 = new CompiledRoute(
            '/base/group/a',
            '/base/group/a/index.html',
            new ControllerResource($controller1, []),
            'a',
        );
        $expected2 = new CompiledRoute(
            '/base/group/b',
            '/base/group/b/index.html',
            new ControllerResource($controller2, []),
            'b',
        );

        $group = new Group('/group', $provider);
        $this->visitor->visitGroup($group);

        $actual = iterator_to_array($this->visitor->routes->all());
        self::assertEquals([$expected1, $expected2], $actual, 'Unexpected route compilation result');
    }

    public function testVisitGroupWithProviderService(): void
    {
        $controller1 = static fn() => 'A';
        $route1 = new Route('/a', $controller1, 'a');

        $controller2 = static fn() => 'B';
        $route2 = new Route('/b', $controller2, 'b');

        $provider = new class ($route1, $route2) implements RouteProviderInterface {
            public function __construct(
                private Route $route1,
                private Route $route2,
            ) {}

            public function routes(): iterable
            {
                yield $this->route1;
                yield $this->route2;
            }
        };

        $reference = 'provider_reference';
        $this->serviceLocator
            ->expects($this->once())
            ->method('get')
            ->with($reference)
            ->willReturn($provider);

        $expected1 = new CompiledRoute(
            '/base/group/a',
            '/base/group/a/index.html',
            new ControllerResource($controller1, []),
            'a',
        );
        $expected2 = new CompiledRoute(
            '/base/group/b',
            '/base/group/b/index.html',
            new ControllerResource($controller2, []),
            'b',
        );

        $group = new Group('/group', $reference);
        $this->visitor->visitGroup($group);

        $actual = iterator_to_array($this->visitor->routes->all());
        self::assertEquals([$expected1, $expected2], $actual, 'Unexpected route compilation result');
    }
}
