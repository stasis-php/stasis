<?php

declare(strict_types=1);

namespace Stasis\Tests\Router\Compiler;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Stasis\EventDispatcher\EventDispatcher;
use Stasis\Router\Compiler\CompiledRoute;
use Stasis\Router\Compiler\RouteCompiler;
use Stasis\Router\Compiler\Resource\ControllerResource;
use Stasis\Router\Compiler\Resource\FileResource;
use Stasis\Router\Route\Asset;
use Stasis\Router\Route\Route;
use Stasis\ServiceLocator\ServiceLocator;

class RouteCompilerTest extends TestCase
{
    private MockObject&ServiceLocator $serviceLocator;
    private MockObject&EventDispatcher $dispatcher;
    private RouteCompiler $compiler;

    public function setUp(): void
    {
        $this->serviceLocator = $this->createMock(ServiceLocator::class);
        $this->dispatcher = $this->createMock(EventDispatcher::class);
        $this->compiler = new RouteCompiler('/base', $this->serviceLocator, $this->dispatcher);
    }

    public function testCompile(): void
    {
        $controller = static fn() => 'OK';
        $routes = [
            new Route('/page', $controller, 'page', ['a' => 1]),
            new Asset('/assets/style.css', '/src/style.css', 'style'),
        ];

        $expectedRoute = new CompiledRoute(
            '/base/page',
            '/base/page/index.html',
            new ControllerResource($controller, ['a' => 1]),
            'page',
        );
        $expectedAsset = new CompiledRoute(
            '/base/assets/style.css',
            '/base/assets/style.css',
            new FileResource('/src/style.css'),
            'style',
        );

        $actual = $this->compiler->compile($routes)->all();
        self::assertEquals([$expectedRoute, $expectedAsset], $actual, 'Unexpected compiled routes returned');
    }

    public function testCompileEmpty(): void
    {
        $compiler = new RouteCompiler('/base', $this->serviceLocator, $this->dispatcher);
        $actual = $compiler->compile([])->all();

        self::assertSame([], $actual, 'Expected no compiled routes');
    }
}
