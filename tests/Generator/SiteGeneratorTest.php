<?php

declare(strict_types=1);

namespace Stasis\Tests\Generator;

use PHPUnit\Framework\TestCase;
use Stasis\Exception\LogicException;
use Stasis\Generator\Distribution\DistributionInterface;
use Stasis\Generator\SiteGenerator;
use Stasis\Router\Compiler\CompiledRoute;
use Stasis\Router\Compiler\CompiledRouteCollection;
use Stasis\Router\Compiler\Resource\ControllerResource;
use Stasis\Router\Compiler\Resource\FileResource;
use Stasis\ServiceLocator\ServiceLocator;
use Stasis\Tests\Doubles\Generator\MockDistribution;

class SiteGeneratorTest extends TestCase
{
    public function testGenerate(): void
    {
        $serviceLocator = $this->createMock(ServiceLocator::class);
        $distribution = new MockDistribution();

        $routes = new CompiledRouteCollection();
        $routes->add(new CompiledRoute(
            path: '/index',
            distPath: 'index.html',
            resource: new ControllerResource(static fn() => 'hello'),
            name: 'home',
        ));
        $routes->add(new CompiledRoute(
            path: '/asset',
            distPath: 'assets/style.css',
            resource: new FileResource('/path/to/source/style.css'),
            name: 'asset',
        ));

        $generator = new SiteGenerator($serviceLocator, $distribution);
        $generator->generate($routes, false);

        self::assertSame(1, $distribution->cleared, 'Distribution not cleared.');

        self::assertCount(1, $distribution->writes, 'Unexpected file write operations count.');
        self::assertSame('index.html', $distribution->writes[0]['path'], 'Unexpected file path.');
        self::assertSame('hello', $distribution->writes[0]['content'], 'Unexpected file content.');

        self::assertCount(1, $distribution->copies, 'Unexpected file copy operations count.');
        self::assertSame('/path/to/source/style.css', $distribution->copies[0]['source'], 'Unexpected source path.');
        self::assertSame('assets/style.css', $distribution->copies[0]['dest'], 'Unexpected destination path.');
    }

    public function testGenerateSymlinkUnsupported(): void
    {
        $serviceLocator = $this->createMock(ServiceLocator::class);
        $distribution = $this->createMock(DistributionInterface::class);

        $routes = new CompiledRouteCollection();
        $routes->add(new CompiledRoute(
            path: '/file',
            distPath: 'file.txt',
            resource: new FileResource('/src/file.txt'),
            name: 'file',
        ));

        $generator = new SiteGenerator($serviceLocator, $distribution);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessageMatches('/distribution does not support symlinks/');
        $generator->generate($routes, true);
    }

    public function testGenerateSymlink(): void
    {
        $serviceLocator = $this->createMock(ServiceLocator::class);
        $distribution = new MockDistribution();

        $routes = new CompiledRouteCollection();
        $routes->add(new CompiledRoute(
            path: '/file',
            distPath: 'assets/img/logo.png',
            resource: new FileResource('/src/assets/img/logo.png'),
            name: 'logo',
        ));

        $generator = new SiteGenerator($serviceLocator, $distribution);
        $generator->generate($routes, true);

        self::assertSame(1, $distribution->cleared, 'Distribution not cleared.');
        self::assertCount(0, $distribution->copies, 'Copy should not be used when symlink is enabled.');
        self::assertCount(1, $distribution->links, 'Unexpected link operations count.');
        self::assertSame('/src/assets/img/logo.png', $distribution->links[0]['source'], 'Unexpected source path.');
        self::assertSame('assets/img/logo.png', $distribution->links[0]['dest'], 'Unexpected destination path.');
    }
}
