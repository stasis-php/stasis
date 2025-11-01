<?php

declare(strict_types=1);

namespace Stasis\Tests\Server;

use PHPUnit\Framework\Attributes\DataProvider;
use Stasis\Exception\LogicException;
use Stasis\Server\Server;
use PHPUnit\Framework\TestCase;

class ServerTest extends TestCase
{
    #[DataProvider('pathDataProvider')]
    public function testValidatePath(string $path, bool $isValid): void
    {
        if ($isValid) {
            $this->expectNotToPerformAssertions();
        } else {
            $this->expectException(LogicException::class);
            $this->expectExceptionMessage('Invalid path');
        }

        new Server($path, 'localhost', 8080);
    }

    /** @return array<mixed> */
    public static function pathDataProvider(): array
    {
        return [
            'current dir' => [__DIR__, true],
            'empty' => ['', false],
            'not existent' => [__DIR__ . '/random_non_existent_path', false],
        ];
    }

    #[DataProvider('portDataProvider')]
    public function testValidatePort(int $port, bool $isValid): void
    {
        if ($isValid) {
            $this->expectNotToPerformAssertions();
        } else {
            $this->expectException(LogicException::class);
            $this->expectExceptionMessage('Invalid port');
        }

        new Server(__DIR__, 'localhost', $port);
    }

    /** @return array<mixed> */
    public static function portDataProvider(): array
    {
        return [
            'valid' => [8080, true],
            'min' => [1, true],
            'max' => [65535, true],
            'zero' => [0, false],
            'overflow' => [65536, false],
            'negative' => [-1, false],
        ];
    }

    #[DataProvider('hostDataProvider')]
    public function testValidateHost(string $host, bool $isValid): void
    {
        if ($isValid) {
            $this->expectNotToPerformAssertions();
        } else {
            $this->expectException(LogicException::class);
            $this->expectExceptionMessage('Invalid host');
        }

        new Server(__DIR__, $host, 8080);
    }

    /** @return array<mixed> */
    public static function hostDataProvider(): array
    {
        return [
            'localhost' => ['localhost', true],
            'domain' => ['example.com', true],
            'ip v4' => ['127.0.0.1', true],
            'ip v6' => ['::1', true],
            'empty' => ['', false],
            'invalid' => ['invalid@host', false],
        ];
    }

    public function testStart(): void
    {
        $server = new Server(__DIR__ . '/fake_dist', 'localhost', 8080);
        $server->start();

        usleep(100_000); // wait for the server to start

        $contents = (string) file_get_contents('http://localhost:8080');
        $contents = trim($contents);
        self::assertSame('It works!', $contents, 'Unexpected response content.');

        $stderr = $server->getStdErrContents();
        self::assertStringContainsString('[200]: GET /', $stderr, 'Missing page served message.');

        $server->stop();
    }

    public function testStop(): void
    {
        $server = new Server(__DIR__ . '/fake_dist', 'localhost', 8080);

        $server->start();
        $isRunning = $server->isRunning();
        self::assertTrue($isRunning, 'Server status is not running.');

        $server->stop();
        $isRunning = $server->isRunning();
        self::assertFalse($isRunning, 'Server status is running.');

        $contents = @file_get_contents('http://localhost:8080');
        self::assertFalse($contents, 'Server responded after stopping.');
    }
}
