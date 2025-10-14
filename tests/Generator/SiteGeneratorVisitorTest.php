<?php

declare(strict_types=1);

namespace Stasis\Tests\Generator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Stasis\Controller\ControllerInterface;
use Stasis\Exception\LogicException;
use Stasis\Exception\RuntimeException;
use Stasis\Generator\Distribution\DistributionInterface;
use Stasis\Generator\Distribution\SymlinkDistributionInterface;
use Stasis\Generator\SiteGeneratorVisitor;
use Stasis\Router\Compiler\Resource\ControllerResource;
use Stasis\Router\Compiler\Resource\FileResource;
use Stasis\Router\Router;
use Stasis\ServiceLocator\ServiceLocator;

class SiteGeneratorVisitorTest extends TestCase
{
    private MockObject&ServiceLocator $locator;
    private MockObject&DistributionInterface $distribution;
    private MockObject&Router $router;
    private SiteGeneratorVisitor $visitor;

    public function setUp(): void
    {
        $this->locator = $this->createMock(ServiceLocator::class);
        $this->distribution = $this->createMock(DistributionInterface::class);
        $this->router = $this->createMock(Router::class);

        $this->visitor = new SiteGeneratorVisitor(
            $this->locator,
            $this->distribution,
            $this->router,
            '/page.html',
            false,
        );
    }

    public function testConstructorSymlinkNotSupported(): void
    {
        $distribution = $this->createMock(DistributionInterface::class);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('does not support symlinks');

        new SiteGeneratorVisitor($this->locator, $distribution, $this->router, '/', true);
    }

    public function testVisitControllerInstance(): void
    {
        $resource = new ControllerResource(new class implements ControllerInterface {
            public function render(Router $router, array $parameters): string
            {
                return 'test content';
            }
        });

        $this->distribution
            ->expects($this->once())
            ->method('write')
            ->with(
                self::identicalTo('/page.html'),
                self::identicalTo('test content'),
            );

        $this->visitor->visitController($resource);
    }

    public function testVisitControllerReference(): void
    {
        $reference = 'controller_reference';

        $controller = new class implements ControllerInterface {
            public function render(Router $router, array $parameters): string
            {
                return 'test content';
            }
        };
        $resource = new ControllerResource($reference);

        $this->locator
            ->expects($this->once())
            ->method('get')
            ->with(self::identicalTo($reference))
            ->willReturn($controller);

        $this->distribution
            ->expects($this->once())
            ->method('write')
            ->with(
                self::identicalTo('/page.html'),
                self::identicalTo('test content'),
            );

        $this->visitor->visitController($resource);
    }

    public function testVisitControllerClosure(): void
    {
        $closure = static function (Router $router, array $parameters): string {
            return 'test content';
        };

        $this->distribution
            ->expects($this->once())
            ->method('write')
            ->with(
                self::identicalTo('/page.html'),
                self::identicalTo('test content'),
            );

        $this->visitor->visitController(new ControllerResource($closure));
    }

    public function testVisitControllerStream(): void
    {
        $resource = new ControllerResource(new class implements ControllerInterface {
            /** @return resource */
            public function render(Router $router, array $parameters)
            {
                /** @var resource $stream */
                $stream = fopen('php://memory', 'rb+');
                fwrite($stream, 'tets content');
                rewind($stream);
                return $stream;
            }
        });

        $this->distribution
            ->expects($this->once())
            ->method('write')
            ->with(
                self::equalTo('/page.html'),
                self::callback(static function ($content): bool {
                    self::assertIsResource($content, 'Stream resource expected');
                    self::assertSame('tets content', stream_get_contents($content), 'Unexpected stream content');
                    return true;
                }),
            );

        $this->visitor->visitController($resource);
    }

    public function testVisitControllerInvalidType(): void
    {
        $resource = new ControllerResource(new class implements ControllerInterface {
            public function render(Router $router, array $parameters) // @phpstan-ignore-line
            {
                return 123; // @phpstan-ignore-line
            }
        });

        $this->distribution
            ->expects($this->never())
            ->method('write');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unexpected return type');

        $this->visitor->visitController($resource);
    }

    public function testVisitFileCopy(): void
    {
        $resource = new FileResource('/path/to/source.css');

        $this->distribution
            ->expects($this->once())
            ->method('copy')
            ->with(
                self::equalTo('/path/to/source.css'),
                self::equalTo('/page.html'),
            );

        $this->visitor->visitFile($resource);
    }

    public function testVisitFileSymlink(): void
    {
        $resource = new FileResource('/path/to/source.css');
        $distribution = $this->createMock(SymlinkDistributionInterface::class);

        $distribution->expects($this->never())->method('copy');
        $distribution
            ->expects($this->once())
            ->method('link')
            ->with(
                self::equalTo('/path/to/source.css'),
                self::equalTo('/page.html'),
            );

        $visitor = new SiteGeneratorVisitor(
            $this->locator,
            $distribution,
            $this->router,
            '/page.html',
            true,
        );
        $visitor->visitFile($resource);
    }
}
