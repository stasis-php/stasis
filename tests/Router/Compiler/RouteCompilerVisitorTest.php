<?php

declare(strict_types=1);

namespace Stasis\Tests\Router\Compiler;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Stasis\EventDispatcher\Event\RouteCompiled\RouteCompiledEvent;
use Stasis\EventDispatcher\EventDispatcher;
use Stasis\Router\Compiler\CompiledRoute;
use Stasis\Router\Compiler\RouteCompilerVisitor;
use Stasis\Router\Compiler\Resource\ControllerResource;
use Stasis\Router\Compiler\Resource\FileResource;
use Stasis\Router\Route\Asset;
use Stasis\Router\Route\Group;
use Stasis\Router\Route\Route;
use Stasis\Router\Route\RouteProviderInterface;
use Stasis\ServiceLocator\ServiceLocator;

class RouteCompilerVisitorTest extends TestCase
{
    private MockObject&ServiceLocator $serviceLocator;
    private MockObject&EventDispatcher $dispatcher;
    private RouteCompilerVisitor $visitor;

    public function setUp(): void
    {
        $this->serviceLocator = $this->createMock(ServiceLocator::class);
        $this->dispatcher = $this->createMock(EventDispatcher::class);
        $this->visitor = new RouteCompilerVisitor('/base', $this->serviceLocator, $this->dispatcher);
    }

    #[DataProvider('visitRouteProvider')]
    public function testVisitRoute(string $path, string $expectedPath, string $expectedDist): void
    {
        $controller = static fn() => 'OK';
        $route = new Route($path, $controller, 'example_name', ['a' => 1]);

        $expected = new CompiledRoute(
            $expectedPath,
            $expectedDist,
            new ControllerResource($controller, ['a' => 1]),
            'example_name',
        );

        $event = new RouteCompiledEvent($expected);
        $this->dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with(self::equalTo($event));

        $this->visitor->visitRoute($route);
        $actual = $this->visitor->routes->all();
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

        $expected = new CompiledRoute(
            '/base/images/logo.png',
            '/base/images/logo.png',
            new FileResource('/src/assets/logo.png'),
            'logo',
        );

        $event = new RouteCompiledEvent($expected);
        $this->dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with(self::equalTo($event));

        $this->visitor->visitAsset($asset);
        $actual = $this->visitor->routes->all();
        self::assertEquals([$expected], $actual, 'Unexpected route compilation result');
    }

    public function testVisitGroupWithIterable(): void
    {
        $controller = static fn() => 'OK';

        $group = new Group('/blog', [
            new Route('/post', $controller, 'post'),
            new Asset('/assets/style.css', '/src/style.css', 'style'),
        ]);

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

        $invokedCount = $this->exactly(2);
        $this->dispatcher
            ->expects($invokedCount)
            ->method('dispatch')
            ->willReturnCallback(function ($actualEvent) use ($invokedCount, $expectedRoute, $expectedAsset) {
                $invocation = $invokedCount->numberOfInvocations();
                $expectedEvent = match ($invocation) {
                    1 => new RouteCompiledEvent($expectedRoute),
                    2 => new RouteCompiledEvent($expectedAsset),
                    default => self::fail('Unexpected "dispatch" invocation count: ' . $invocation),
                };

                self::assertEquals($expectedEvent, $actualEvent, 'Unexpected event dispatched');
            });

        $this->visitor->visitGroup($group);

        $actual = $this->visitor->routes->all();
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

        $invokedCount = $this->exactly(2);
        $this->dispatcher
            ->expects($invokedCount)
            ->method('dispatch')
            ->willReturnCallback(function ($actualEvent) use ($invokedCount, $expected1, $expected2) {
                $invocation = $invokedCount->numberOfInvocations();
                $expectedEvent = match ($invocation) {
                    1 => new RouteCompiledEvent($expected1),
                    2 => new RouteCompiledEvent($expected2),
                    default => self::fail('Unexpected "dispatch" invocation count: ' . $invocation),
                };

                self::assertEquals($expectedEvent, $actualEvent, 'Unexpected event dispatched');
            });

        $group = new Group('/group', $provider);
        $this->visitor->visitGroup($group);

        $actual = $this->visitor->routes->all();
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
        $this->serviceLocator->method('get')->with($reference)->willReturn($provider);

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

        $invokedCount = $this->exactly(2);
        $this->dispatcher
            ->expects($invokedCount)
            ->method('dispatch')
            ->willReturnCallback(function ($actualEvent) use ($invokedCount, $expected1, $expected2) {
                $invocation = $invokedCount->numberOfInvocations();
                $expectedEvent = match ($invocation) {
                    1 => new RouteCompiledEvent($expected1),
                    2 => new RouteCompiledEvent($expected2),
                    default => self::fail('Unexpected "dispatch" invocation count: ' . $invocation),
                };

                self::assertEquals($expectedEvent, $actualEvent, 'Unexpected event dispatched');
            });

        $group = new Group('/group', $reference);
        $this->visitor->visitGroup($group);

        $actual = $this->visitor->routes->all();
        self::assertEquals([$expected1, $expected2], $actual, 'Unexpected route compilation result');
    }
}
