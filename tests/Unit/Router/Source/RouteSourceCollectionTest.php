<?php

declare(strict_types=1);

namespace Stasis\Tests\Unit\Router\Source;

use PHPUnit\Framework\TestCase;
use Stasis\Exception\LogicException;
use Stasis\Router\Route\Route;
use Stasis\Router\Source\RouteSource;
use Stasis\Router\Source\RouteSourceCollection;

class RouteSourceCollectionTest extends TestCase
{
    public function testAdd(): void
    {
        $collection = new RouteSourceCollection();

        $route1 = new Route('/page1', 'reference_1');
        $route2 = new Route('/page2', 'reference_2');
        $route3 = new Route('/page3', 'reference_3');

        $source1 = new RouteSource('source1', [$route1]);
        $source2 = new RouteSource('source2', [$route2, $route3]);

        $collection->add($source1);
        $collection->add($source2);

        $iterated = iterator_to_array($collection);

        self::assertSame([$route1, $route2, $route3], $iterated, 'Unexpected routes returned');
    }

    public function testAddDuplicateNameThrows(): void
    {
        $collection = new RouteSourceCollection();
        $route = new Route('/page', 'reference');

        $collection->add(new RouteSource('duplicated', [$route]));

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Route source "duplicated" already exists.');
        $collection->add(new RouteSource('duplicated', [$route]));
    }
}
