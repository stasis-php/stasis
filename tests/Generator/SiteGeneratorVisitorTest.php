<?php

declare(strict_types=1);

namespace Stasis\Tests\Generator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Stasis\Controller\ControllerInterface;
use Stasis\Exception\LogicException;
use Stasis\Exception\RuntimeException;
use Stasis\Generator\Distribution\DistributionInterface;
use Stasis\Generator\SiteGeneratorVisitor;
use Stasis\Router\Compiler\Resource\ControllerResource;
use Stasis\Router\Compiler\Resource\FileResource;
use Stasis\Router\Router;
use Stasis\ServiceLocator\ServiceLocator;
use Stasis\Tests\Generator\Distribution\TestDistribution;

class SiteGeneratorVisitorTest extends TestCase
{
    private MockObject&ServiceLocator $locator;
    private TestDistribution $distribution;
    private MockObject&Router $router;
    private SiteGeneratorVisitor $visitor;

    public function setUp(): void
    {
        $this->locator = $this->createMock(ServiceLocator::class);
        $this->distribution = new TestDistribution();
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

        $this->visitor->visitController($resource);

        self::assertCount(1, $this->distribution->writes, 'Unexpected write operations count.');
        self::assertSame('/page.html', $this->distribution->writes[0]['path'], 'Unexpected file path.');
        self::assertSame('test content', $this->distribution->writes[0]['content'], 'Unexpected file content.');
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

        $this->visitor->visitController($resource);

        self::assertCount(1, $this->distribution->writes, 'Unexpected write operations count.');
        self::assertSame('/page.html', $this->distribution->writes[0]['path'], 'Unexpected file path.');
        self::assertSame('test content', $this->distribution->writes[0]['content'], 'Unexpected file content.');
    }

    public function testVisitControllerClosure(): void
    {
        $closure = static function (Router $router, array $parameters): string {
            return 'test content';
        };

        $this->visitor->visitController(new ControllerResource($closure));

        self::assertCount(1, $this->distribution->writes, 'Unexpected write operations count.');
        self::assertSame('/page.html', $this->distribution->writes[0]['path'], 'Unexpected file path.');
        self::assertSame('test content', $this->distribution->writes[0]['content'], 'Unexpected file content.');
    }

    public function testVisitControllerStream(): void
    {
        $resource = new ControllerResource(new class implements ControllerInterface {
            /** @return resource */
            public function render(Router $router, array $parameters)
            {
                /** @var resource $stream */
                $stream = fopen('php://memory', 'rb+');
                fwrite($stream, 'test content');
                rewind($stream);
                return $stream;
            }
        });

        $this->visitor->visitController($resource);

        self::assertCount(1, $this->distribution->writes, 'Unexpected write operations count.');
        self::assertSame('/page.html', $this->distribution->writes[0]['path'], 'Unexpected file path.');
        self::assertSame('test content', $this->distribution->writes[0]['content'], 'Unexpected file content.');
    }

    public function testVisitControllerInvalidType(): void
    {
        $resource = new ControllerResource(new class implements ControllerInterface {
            public function render(Router $router, array $parameters) // @phpstan-ignore-line
            {
                return 123; // @phpstan-ignore-line
            }
        });

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unexpected return type');

        $this->visitor->visitController($resource);

        self::assertCount(0, $this->distribution->writes, 'Unexpected write operations count.');
    }

    public function testVisitFileCopy(): void
    {
        $resource = new FileResource('/path/to/source.css');

        $this->visitor->visitFile($resource);

        self::assertCount(1, $this->distribution->copies, 'Unexpected copy operations count.');
        self::assertSame('/path/to/source.css', $this->distribution->copies[0]['source'], 'Unexpected source path.');
        self::assertSame('/page.html', $this->distribution->copies[0]['dest'], 'Unexpected dest path.');
    }

    public function testVisitFileSymlink(): void
    {
        $resource = new FileResource('/path/to/source.css');
        $visitor = new SiteGeneratorVisitor(
            $this->locator,
            $this->distribution,
            $this->router,
            '/page.html',
            true,
        );
        $visitor->visitFile($resource);

        self::assertCount(0, $this->distribution->copies, 'Unexpected copy operations count.');

        self::assertCount(1, $this->distribution->links, 'Unexpected link operations count.');
        self::assertSame('/path/to/source.css', $this->distribution->links[0]['source'], 'Unexpected source path.');
        self::assertSame('/page.html', $this->distribution->links[0]['dest'], 'Unexpected dest path.');
    }
}
