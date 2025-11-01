<?php

declare(strict_types=1);

namespace Stasis\Tests\Console\Command;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Stasis\Console\Command\ServerCommand;
use Stasis\Generator\Distribution\DistributionInterface;
use Stasis\Generator\Distribution\LocalDistributionInterface;
use Stasis\Kernel;
use Stasis\Server\Server;
use Stasis\Server\ServerFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

class ServerCommandTest extends TestCase
{
    private MockObject&Kernel $kernel;
    private MockObject&LocalDistributionInterface $distribution;
    private MockObject&ServerFactory $serverFactory;
    private ServerCommand $command;

    public function setUp(): void
    {
        $this->kernel = $this->createMock(Kernel::class);
        $this->distribution = $this->createMock(LocalDistributionInterface::class);
        $this->serverFactory = $this->createMock(ServerFactory::class);

        $this->command = new ServerCommand(
            $this->distribution,
            $this->serverFactory,
        );
    }

    public function testName(): void
    {
        $name = ServerCommand::name();
        self::assertSame('server', $name);
    }

    public function testDescription(): void
    {
        $description = ServerCommand::description();
        self::assertSame('Start the development server', $description);
    }

    public function testCreate(): void
    {
        $command = ServerCommand::create($this->kernel);
        self::assertInstanceOf(ServerCommand::class, $command);
    }

    public function testConfigure(): void
    {
        $name = $this->command->getName();
        self::assertSame(ServerCommand::name(), $name, 'Unexpected name.');

        $description = $this->command->getDescription();
        self::assertSame(ServerCommand::description(), $description, 'Unexpected description.');

        $help = $this->command->getHelp();
        self::assertNotEmpty($help, 'Command help should not be empty.');

        $definition = $this->command->getDefinition();

        $hasHostOption = $definition->hasOption('host');
        self::assertTrue($hasHostOption, 'Host option not defined.');

        $hasPortOption = $definition->hasOption('port');
        self::assertTrue($hasPortOption, 'Port option not defined.');
    }

    public function testExecute(): void
    {
        $path = '/path/to/dist';
        $this->distribution->method('path')->willReturn($path);

        /** @var MockObject&Server $server */
        $server = $this->createMock(Server::class);
        $server->expects($this->once())->method('start');
        $server->method('getStdOutContents')->willReturn("STDOUT line\n");
        $server->method('getStdErrContents')->willReturn("STDERR line\n");
        $server->expects($this->once())->method('isRunning')->willReturn(false);
        $server->expects($this->once())->method('stop')->willReturn(0);

        $this->serverFactory->method('create')->willReturn($server);

        $tester = new CommandTester($this->command);
        $exitCode = $tester->execute([]);

        self::assertSame(Command::SUCCESS, $exitCode, 'Unexpected exit code.');

        $display = $tester->getDisplay();
        self::assertStringContainsString('Stasis Development Server', $display, 'Missing server name.');
        self::assertStringContainsString('http://localhost:8000', $display, 'Missing server address.');
        self::assertStringContainsString($path, $display, 'Missing distribution path.');
        // default verbosity should not forward the server stdout/stderr
        self::assertStringNotContainsString('STDOUT line', $display, 'Unexpected stdout line.');
        self::assertStringNotContainsString('STDERR line', $display, 'Unexpected stderr line.');
    }

    public function testExecuteHostPort(): void
    {
        $path = '/path/to/dist';
        $this->distribution->method('path')->willReturn($path);

        /** @var MockObject&Server $server */
        $server = $this->createMock(Server::class);
        $server->expects($this->once())->method('start');
        $server->method('getStdOutContents')->willReturn("STDOUT line\n");
        $server->method('getStdErrContents')->willReturn("STDERR line\n");
        $server->expects($this->once())->method('isRunning')->willReturn(false);
        $server->expects($this->once())->method('stop')->willReturn(0);

        $this->serverFactory->method('create')->willReturn($server);

        $tester = new CommandTester($this->command);

        $exitCode = $tester->execute([
            '--host' => '127.0.0.1',
            '--port' => '8888',
        ]);

        self::assertSame(Command::SUCCESS, $exitCode, 'Unexpected exit code.');

        $display = $tester->getDisplay();
        self::assertStringContainsString('Stasis Development Server', $display, 'Missing server name.');
        self::assertStringContainsString('http://127.0.0.1:8888', $display, 'Missing server address.');
        self::assertStringContainsString($path, $display, 'Missing distribution path.');
        // default verbosity should not forward the server stdout/stderr
        self::assertStringNotContainsString('STDOUT line', $display, 'Unexpected stdout line.');
        self::assertStringNotContainsString('STDERR line', $display, 'Unexpected stderr line.');
    }

    public function testExecuteVerbose(): void
    {
        $path = '/path/to/dist';
        $this->distribution->method('path')->willReturn($path);

        /** @var MockObject&Server $server */
        $server = $this->createMock(Server::class);
        $server->expects($this->once())->method('start');
        $server->method('getStdOutContents')->willReturn("STDOUT line\n");
        $server->method('getStdErrContents')->willReturn("STDERR line\n");
        $server->expects($this->once())->method('isRunning')->willReturn(false);
        $server->expects($this->once())->method('stop')->willReturn(0);

        $this->serverFactory->method('create')->willReturn($server);

        $tester = new CommandTester($this->command);

        $exitCode = $tester->execute([], [
            'verbosity' => OutputInterface::VERBOSITY_VERBOSE,
        ]);

        self::assertSame(Command::SUCCESS, $exitCode, 'Unexpected exit code.');

        $display = $tester->getDisplay();
        self::assertStringContainsString('Stasis Development Server', $display, 'Missing server name.');
        self::assertStringContainsString('http://localhost:8000', $display, 'Missing server address.');
        self::assertStringContainsString($path, $display, 'Missing distribution path.');
        // in verbose mode, stdout and stderr from the server must be forwarded
        self::assertStringContainsString('STDOUT line', $display, 'Missing stdout line.');
        self::assertStringContainsString('STDERR line', $display, 'Missing stderr line.');
    }

    public function testUnsupportedDistribution(): void
    {
        // plain distribution (not local) should fail
        $distribution = $this->createMock(DistributionInterface::class);
        $factory = $this->createMock(ServerFactory::class);
        $command = new ServerCommand($distribution, $factory);

        $tester = new CommandTester($command);
        $exitCode = $tester->execute([]);

        self::assertSame(Command::FAILURE, $exitCode, 'Expected failure exit code.');
        $display = $tester->getDisplay();
        self::assertStringContainsString('Configured distribution does not support local server.', $display);
    }
}
