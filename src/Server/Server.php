<?php

declare(strict_types=1);

namespace Stasis\Server;

use Stasis\Exception\LogicException;
use Stasis\Exception\RuntimeException;

/**
 * @internal
 */
class Server
{
    /** @var resource|null|false */
    private $process = null;

    /** @var resource|null */
    private $stdout = null;

    /** @var resource|null */
    private $stderr = null;

    private int $exitCode = 0;

    public function __construct(
        private readonly string $path,
        private readonly string $host,
        private readonly int $port,
    ) {
        $this->validatePath($path);
        $this->validateHost($host);
        $this->validatePort($port);
    }

    public function start(): void
    {
        $address = $this->host . ':' . $this->port;
        $router = __DIR__ . '/router.php';
        $command = [PHP_BINARY, '-S', $address, '-t', $this->path, $router];

        $descriptorSpec = [
            0 => ['pipe', 'r'], // stdin
            1 => ['pipe', 'w'], // stdout
            2 => ['pipe', 'w'], // stderr
        ];

        $pipes = [];
        $this->process = proc_open($command, $descriptorSpec, $pipes, $this->path);
        if (!is_resource($this->process)) {
            $commandString = implode(' ', $command);
            throw new RuntimeException(sprintf('Failed to start the server process "%s".', $commandString));
        }

        $this->registerShutdownFunction();

        $stdin = &$pipes[0];
        fclose($stdin); // close STDIN since it's not used

        $this->stdout = &$pipes[1];
        stream_set_blocking($this->stdout, false);

        $this->stderr = &$pipes[2];
        stream_set_blocking($this->stderr, false);
    }

    public function stop(): int
    {
        if (is_resource($this->process)) {
            proc_terminate($this->process); // send SIGTERM
            $this->exitCode = proc_close($this->process);
        }
        $this->process = null;

        if (is_resource($this->stdout)) {
            fclose($this->stdout);
        }
        $this->stdout = null;

        if (is_resource($this->stderr)) {
            fclose($this->stderr);
        }
        $this->stderr = null;

        return $this->exitCode;
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
    private function registerShutdownFunction(): void
    {
        register_shutdown_function($this->stop(...));
    }

    /**
     * @param resource|mixed $resource
     */
    private function getContents($resource): string
    {
        if (!is_resource($resource)) {
            return '';
        }

        return stream_get_contents($resource);
    }

    private function validatePath(string $path): void
    {
        if (is_dir($path)) {
            return;
        }

        throw new LogicException(sprintf(
            'Invalid path value "%s" provided. Directory with the specified path does not exist.',
            $path,
        ));
    }

    private function validateHost(string $host): void
    {
        if (filter_var($host, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
            return;
        }

        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return;
        }

        throw new LogicException(sprintf(
            'Invalid host value "%s" provided. Expected a valid IP address or host name.',
            $host,
        ));
    }

    private function validatePort(int $port): void
    {
        if ($port < 1 || $port > 65535) {
            throw new LogicException(sprintf(
                'Invalid port value "%d" provided. Port must be between 1 and 65535.',
                $port,
            ));
        }
    }
}
