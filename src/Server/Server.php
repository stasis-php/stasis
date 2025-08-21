<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Server;

use Vstelmakh\Stasis\Exception\RuntimeException;

class Server
{
    /** @var resource */
    private $process = null;

    /** @var resource */
    private $stdout = null;

    /** @var resource */
    private $stderr = null;

    public function __construct(
        private readonly string $path,
        private readonly string $host,
        private readonly int $port,
    ) {}

    public function start(): void
    {
        $address = $this->host . ':' . $this->port;
        $command = [PHP_BINARY, '-S', $address, '-t', $this->path];
        $descriptorSpec = [
            0 => ['pipe', 'r'], // stdin
            1 => ['pipe', 'w'], // stdout
            2 => ['pipe', 'w'], // stderr
        ];

        $pipes = [];
        $this->process = proc_open($command, $descriptorSpec, $pipes);
        if (!is_resource($this->process)) {
            $commandString = implode(' ', $command);
            throw new RuntimeException(sprintf('Failed to start the server process "%s".', $commandString));
        }

        $this->registerShutdownFunction($this->process);

        $stdin = &$pipes[0];
        fclose($stdin); // close STDIN since it's not used

        $this->stdout = &$pipes[1];
        stream_set_blocking($this->stdout, false);

        $this->stderr = &$pipes[2];
        stream_set_blocking($this->stderr, false);
    }

    public function stop(): int
    {
        if (is_resource($this->stdout)) {
            fclose($this->stdout);
        }
        $this->stdout = null;

        if (is_resource($this->stderr)) {
            fclose($this->stderr);
        }
        $this->stderr = null;

        $exitCode = 0;

        if (is_resource($this->process)) {
            $exitCode = proc_close($this->process);
        }
        $this->process = null;

        return $exitCode;
    }

    public function isRunning(): bool
    {
        if (!is_resource($this->process)) {
            return false;
        }

        $status = proc_get_status($this->process);
        return $status['running'];
    }

    public function getStdOutContents(): string
    {
        return $this->getContents($this->stdout);
    }

    public function getStdErrContents(): string
    {
        return $this->getContents($this->stderr);
    }

    /**
     * Ensure the server stops when the script terminates
     */
    private function registerShutdownFunction($process): void
    {
        register_shutdown_function(function() use ($process) {
            proc_terminate($process); // send SIGTERM
            $this->stop();
        });
    }

    private function getContents($resource): string
    {
        if (!is_resource($resource)) {
            return '';
        }

        return stream_get_contents($resource);
    }
}
