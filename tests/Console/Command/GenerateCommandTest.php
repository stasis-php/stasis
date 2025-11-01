<?php

declare(strict_types=1);

namespace Stasis\Tests\Console\Command;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Stasis\Console\Command\GenerateCommand;
use Stasis\Generator\SiteGenerator;
use Stasis\Kernel;
use Stasis\Router\Compiler\CompiledRouteCollection;
use Stasis\Router\Compiler\RouteCompiler;
use Stasis\Router\Route\RouteInterface;
use Stasis\Stopwatch\Stopwatch;
use Symfony\Component\Console\Tester\CommandTester;

class GenerateCommandTest extends TestCase
{
    private MockObject&Kernel $kernel;
    private MockObject&SiteGenerator $generator;
    private MockObject&RouteCompiler $compiler;
    /** @var iterable<RouteInterface> */
    private iterable $routes;
    private MockObject&Stopwatch $stopwatch;
    private GenerateCommand $command;

    public function setUp(): void
    {
        $this->kernel = $this->createMock(Kernel::class);
        $this->generator = $this->createMock(SiteGenerator::class);
        $this->compiler = $this->createMock(RouteCompiler::class);
        $this->routes = [];
        $this->stopwatch = $this->createMock(Stopwatch::class);

        $this->command = new GenerateCommand(
            $this->generator,
            $this->compiler,
            $this->routes,
            $this->stopwatch,
        );
    }

    public function testName(): void
    {
        $name = GenerateCommand::name();
        self::assertSame('generate', $name);
    }

    public function testDescription(): void
    {
        $description = GenerateCommand::description();
        self::assertSame('Generate static site from specified routes', $description);
    }

    public function testCreate(): void
    {
        $command = GenerateCommand::create($this->kernel);
        self::assertInstanceOf(GenerateCommand::class, $command);
    }

    public function testConfigure(): void
    {
        $name = $this->command->getName();
        self::assertSame(GenerateCommand::name(), $name, 'Unexpected command name.');

        $description = $this->command->getDescription();
        self::assertSame(GenerateCommand::description(), $description, 'Unexpected command description.');

        $help = $this->command->getHelp();
        self::assertNotEmpty($help, 'Command help should not be empty.');

        $definition = $this->command->getDefinition();
        $hasSymlinkOption = $definition->hasOption('symlink');
        self::assertTrue($hasSymlinkOption, 'Symlink option not defined.');
    }

    #[DataProvider('executeDataProvider')]
    public function testExecute(array $input, bool $isSymlink): void
    {
        $compiledRoutes = new CompiledRouteCollection();

        $this->compiler
            ->expects($this->once())
            ->method('compile')
            ->with([])
            ->willReturn($compiledRoutes);

        $this->generator
            ->expects($this->once())
            ->method('generate')
            ->with($compiledRoutes, $isSymlink);

        $this->stopwatch->method('duration')->willReturn(3.4579);
        $this->stopwatch->method('memory')->willReturn(3_785_016);

        $tester = new CommandTester($this->command);

        $exitCode = $tester->execute($input);
        self::assertSame(0, $exitCode, 'Unexpected exit code.');

        $display = $tester->getDisplay();
        self::assertStringContainsString('Generated successfully', $display, 'Missing success message.');
        self::assertStringContainsString('in 3.46 seconds', $display, 'Unexpected time usage message.');
        self::assertStringContainsString('3.61 MB memory used', $display, 'Unexpected memory usage message.');
    }

    /**
     * @return array<mixed>
     */
    public static function executeDataProvider(): array
    {
        return [
            'default' => [[], false],
            'symlink' => [['--symlink' => true], true],
        ];
    }
}
