<?php

declare(strict_types=1);

namespace Stasis\Tests\Functional\Server;

use PHPUnit\Framework\TestCase;
use Stasis\Tests\Functional\StasisProcessFactory;
use Symfony\Component\Process\Process;

class ServerTest extends TestCase
{
    private Process $server;

    public function setUp(): void
    {
        $this->server = StasisProcessFactory::create([
            '--config',  __DIR__ . '/config.php',
            'server',
            '--host', 'localhost',
            '--port', '8080',
        ]);

        $this->server->start();
        usleep(200_000); // wait for the server to start
    }

    public function tearDown(): void
    {
        $this->server->stop();
        usleep(50_000); // wait for the server to stop
    }

    public function testServer(): void
    {
        $output = $this->server->getOutput();
        self::assertStringContainsString('Server is running', $output, 'Missing server running message.');

        $contents = file_get_contents('http://localhost:8080');
        self::assertSame("It works!\n", $contents, 'Unexpected server response on index.');

        $contents = file_get_contents('http://localhost:8080/other.html');
        self::assertSame("Other\n", $contents, 'Unexpected server response on other.');

        @file_get_contents('http://localhost:8080/non-existing');
        $statusHeader = $http_response_header[0] ?? null; // is automatically populated by PHP
        if ($statusHeader !== null) {
            preg_match('{HTTP/\S*\s(\d{3})}', $statusHeader, $match);
            $statusCode = (int) $match[1];
        } else {
            $statusCode = null;
        }
        self::assertSame(404, $statusCode, 'Unexpected server response on non-existing.');
    }
}
