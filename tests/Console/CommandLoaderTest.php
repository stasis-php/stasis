<?php

declare(strict_types=1);

namespace Stasis\Tests\Console;

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Stasis\Console\CommandFactoryInterface;
use Stasis\Console\CommandLoader;
use Stasis\Kernel;
use Stasis\Tests\Doubles\Console\StubACommand;
use Stasis\Tests\Doubles\Console\StubBCommand;
use Symfony\Component\Console\Command\LazyCommand;

class CommandLoaderTest extends TestCase
{
    private MockObject&Kernel $kernel;
    private CommandLoader $loader;

    public function setUp(): void
    {
        $this->kernel = $this->createMock(Kernel::class);
        $this->loader = new CommandLoader($this->kernel, [
            StubACommand::class,
            StubBCommand::class,
        ]);
    }

    public function testConstructorInvalidFactory(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            'must implement "%s" interface.',
            CommandFactoryInterface::class,
        ));

        new CommandLoader($this->kernel, [\stdClass::class]); // @phpstan-ignore-line
    }

    public function testGet(): void
    {
        $lazy = $this->loader->get('test:a');
        self::assertInstanceOf(LazyCommand::class, $lazy, 'Unexpected command type.');

        $name = $lazy->getName();
        self::assertSame('test:a', $name, 'Unexpected command name.');

        $description = $lazy->getDescription();
        self::assertSame('Test command A', $description, 'Unexpected command description.');

        $command = $lazy->getCommand();
        self::assertInstanceOf(StubACommand::class, $command, 'Unexpected command type created by lazy command.');
    }

    public function testGetUnknownName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Command with name "missing" not found.');
        $this->loader->get('missing');
    }

    public function testHas(): void
    {
        $hasCommandA = $this->loader->has('test:a');
        self::assertTrue($hasCommandA, 'Command with name "A" should be recognized.');

        $hasCommandB = $this->loader->has('test:b');
        self::assertTrue($hasCommandB, 'Command with name "B" should be recognized.');

        $hasUnknown = $this->loader->has('unknown');
        self::assertFalse($hasUnknown, 'Unknown command should not be recognized.');
    }

    public function testGetNames(): void
    {
        $names = $this->loader->getNames();
        sort($names);
        self::assertSame(['test:a', 'test:b'], $names, 'Unexpected command names.');
    }
}
