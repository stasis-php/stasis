<?php

declare(strict_types=1);

namespace Stasis\Tests\Unit\Router\Compiler;

use PHPUnit\Framework\TestCase;
use Stasis\Exception\LogicException;
use Stasis\Router\Compiler\CompiledRoute;
use Stasis\Router\Compiler\CompiledRouteCollection;
use Stasis\Router\Compiler\Resource\FileResource;

class CompiledRouteCollectionTest extends TestCase
{
    public function testAdd(): void
    {
        $collection = new CompiledRouteCollection();
        $route1 = new CompiledRoute('/a', '/a/index.html', new FileResource('/tmp/a'));
        $route2 = new CompiledRoute('/b', '/b/index.html', new FileResource('/tmp/b'), 'b_name');
        $collection->add($route1)->add($route2);

        $all = $collection->all();
        self::assertSame([$route1, $route2], $all, 'Unexpected routes returned by "all"');

        $iterated = iterator_to_array($collection);
        self::assertSame([$route1, $route2], array_values($iterated), 'Unexpected routes returned by iterator');
    }

    public function testGetByName(): void
    {
        $collection = new CompiledRouteCollection();

        $unnamed = new CompiledRoute('/no-name', '/no-name/index.html', new FileResource('/tmp/none'));
        $named = new CompiledRoute('/named', '/named/index.html', new FileResource('/tmp/named'), 'named_route');

        $collection->add($unnamed)->add($named);

        self::assertNull($collection->getByName('unknown'), 'Should return null for unknown name');
        self::assertSame($named, $collection->getByName('named_route'), 'Should return the route for existing name');
    }

    public function testAddDuplicatePathThrows(): void
    {
        $collection = new CompiledRouteCollection();
        $collection->add(new CompiledRoute('/duplicate', '/duplicate.html', new FileResource('/tmp/duplicate1')));

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Duplicated route path "/duplicate".');
        $collection->add(new CompiledRoute('/duplicate', '/duplicate.html', new FileResource('/tmp/duplicate2')));
    }

    public function testAddDuplicateNameThrows(): void
    {
        $collection = new CompiledRouteCollection();
        $collection->add(new CompiledRoute('/page1.html', '/page1.html', new FileResource('/tmp/file')));
        $collection->add(new CompiledRoute('/page2.html', '/page2.html', new FileResource('/tmp/file')));

        $collection->add(new CompiledRoute('/page3.html', '/page3.html', new FileResource('/tmp/file'), 'page'));
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Duplicated route name "page".');
        $collection->add(new CompiledRoute('/page4.html', '/page4.html', new FileResource('/tmp/file'), 'page'));
    }
}
