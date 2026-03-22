<?php

declare(strict_types=1);

namespace Stasis\Tests\Unit\Router\Compiler;

use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Stasis\Router\Compiler\CompiledRoute;
use Stasis\Router\Compiler\Resource\ControllerResource;
use Stasis\Router\Compiler\Resource\FileResource;
use Stasis\Router\Compiler\RouteCompiler;
use Stasis\Router\Route\Asset;
use Stasis\Router\Route\Route;
use Stasis\ServiceLocator\ServiceLocator;

class RouteCompilerTest extends TestCase
{
    private Stub&ServiceLocator $serviceLocator;
    private RouteCompiler $compiler;

    #[\Override]
    public function setUp(): void
    {
        $this->serviceLocator = $this->createStub(ServiceLocator::class);
        $this->compiler = new RouteCompiler('/base', $this->serviceLocator);
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

        $collection = $this->compiler->compile($routes);
        $actual = iterator_to_array($collection->all());
        self::assertEquals([$expectedRoute, $expectedAsset], $actual, 'Unexpected compiled routes returned');
    }

    public function testCompileEmpty(): void
    {
        $compiler = new RouteCompiler('/base', $this->serviceLocator);
        $collection = $compiler->compile([]);
        $actual = iterator_to_array($collection->all());

        self::assertSame([], $actual, 'Expected no compiled routes');
    }
}
