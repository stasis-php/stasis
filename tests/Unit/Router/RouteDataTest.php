<?php

declare(strict_types=1);

namespace Stasis\Tests\Unit\Router;

use PHPUnit\Framework\TestCase;
use Stasis\Router\Compiler\CompiledRoute;
use Stasis\Router\Compiler\Resource\FileResource;
use Stasis\Router\RouteData;

class RouteDataTest extends TestCase
{
    public function testFromCompiled(): void
    {
        $route = new CompiledRoute('/page', '/page.html', new FileResource('/src/page.html'), 'page_name');
        $expected = new RouteData('/page', 'page_name');
        $actual = RouteData::fromCompiled($route);
        self::assertEquals($expected, $actual, 'Unexpected route data');
    }
}
