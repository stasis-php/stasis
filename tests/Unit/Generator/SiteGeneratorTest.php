<?php

declare(strict_types=1);

namespace Stasis\Tests\Unit\Generator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Stasis\EventDispatcher\Event\SiteGenerate\SiteGenerateEvent;
use Stasis\EventDispatcher\EventDispatcher;
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
    private MockObject&ServiceLocator $serviceLocator;
    private MockDistribution $distribution;
    private MockObject&EventDispatcher $eventDispatcher;
    private SiteGenerator $siteGenerator;

    public function setUp(): void
    {
        $this->serviceLocator = $this->createMock(ServiceLocator::class);
        $this->distribution = new MockDistribution();
        $this->eventDispatcher = $this->createMock(EventDispatcher::class);
        $this->siteGenerator = new SiteGenerator($this->serviceLocator, $this->distribution, $this->eventDispatcher);
    }

    public function testGenerate(): void
    {
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

        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with(self::isInstanceOf(SiteGenerateEvent::class));

        $this->siteGenerator->generate($routes, false);

        self::assertSame(1, $this->distribution->cleared, 'Distribution not cleared.');

        self::assertCount(1, $this->distribution->writes, 'Unexpected file write operations count.');
        self::assertSame('index.html', $this->distribution->writes[0]['path'], 'Unexpected file path.');
        self::assertSame('hello', $this->distribution->writes[0]['content'], 'Unexpected file content.');

        self::assertCount(1, $this->distribution->copies, 'Unexpected file copy operations count.');
        self::assertSame('/path/to/source/style.css', $this->distribution->copies[0]['source'], 'Unexpected source path.');
        self::assertSame('assets/style.css', $this->distribution->copies[0]['dest'], 'Unexpected destination path.');
    }

    public function testGenerateSymlinkUnsupported(): void
    {
        $routes = new CompiledRouteCollection();
        $routes->add(new CompiledRoute(
            path: '/file',
            distPath: 'file.txt',
            resource: new FileResource('/src/file.txt'),
            name: 'file',
        ));

        $distribution = $this->createMock(DistributionInterface::class);
        $siteGenerator = new SiteGenerator($this->serviceLocator, $distribution, $this->eventDispatcher);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessageMatches('/distribution does not support symlinks/');
        $siteGenerator->generate($routes, true);
    }

    public function testGenerateSymlink(): void
    {
        $routes = new CompiledRouteCollection();
        $routes->add(new CompiledRoute(
            path: '/file',
            distPath: 'assets/img/logo.png',
            resource: new FileResource('/src/assets/img/logo.png'),
            name: 'logo',
        ));

        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with(self::isInstanceOf(SiteGenerateEvent::class));

        $this->siteGenerator->generate($routes, true);

        self::assertSame(1, $this->distribution->cleared, 'Distribution not cleared.');
        self::assertCount(0, $this->distribution->copies, 'Copy should not be used when symlink is enabled.');
        self::assertCount(1, $this->distribution->links, 'Unexpected link operations count.');
        self::assertSame('/src/assets/img/logo.png', $this->distribution->links[0]['source'], 'Unexpected source path.');
        self::assertSame('assets/img/logo.png', $this->distribution->links[0]['dest'], 'Unexpected destination path.');
    }
}
