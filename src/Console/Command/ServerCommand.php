<?php

declare(strict_types=1);

namespace Stasis\Console\Command;

use Stasis\Console\CommandFactoryInterface;
use Stasis\Generator\Distribution\DistributionInterface;
use Stasis\Generator\Distribution\LocalDistributionInterface;
use Stasis\Kernel;
use Stasis\Server\Server;
use Stasis\Server\ServerFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressIndicator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 */
class ServerCommand extends Command implements CommandFactoryInterface
{
    private const string NAME = 'server';
    private const string DESCRIPTION = 'Start the development server';
    private const string OPTION_HOST = 'host';
    private const string OPTION_PORT = 'port';

    private ?Server $server = null;

    public static function name(): string
    {
        return self::NAME;
    }

    public static function description(): string
    {
        return self::DESCRIPTION;
    }

    public static function create(Kernel $kernel): self
    {
        $distribution = $kernel->distribution();
        $serverFactory = new ServerFactory();
        return new self($distribution, $serverFactory);
    }

    public function __construct(
        private readonly DistributionInterface $distribution,
        private readonly ServerFactory $serverFactory,
    ) {
        parent::__construct(self::NAME);
    }

    /** @return array<int> */
    public function getSubscribedSignals(): array
    {
        return [2, 15]; // SIGINT, SIGTERM
    }

    public function handleSignal(int $signal, int|false $previousExitCode = 0): int|false
    {
        $this->server?->stop();
        return false;
    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::DESCRIPTION)
            ->addOption(self::OPTION_HOST, null, InputOption::VALUE_REQUIRED, 'Host to run the server on', 'localhost')
            ->addOption(self::OPTION_PORT, null, InputOption::VALUE_REQUIRED, 'Port to run the server on', 8000)
            ->setHelp('Stasis development server is utilizing PHP built-in web server. Does NOT meant to be used in production environment.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->distribution instanceof LocalDistributionInterface) {
            $output->writeln('Configured distribution does not support local server.');
            return Command::FAILURE;
        }

        $path = $this->distribution->path();
        $host = $input->getOption(self::OPTION_HOST);
        $port = (int) $input->getOption(self::OPTION_PORT);

        $this->printStartMessage($output, $path, $host, $port);

        $server = $this->serverFactory->create($path, $host, $port);
        $this->server = $server;
        $server->start();

        $indicator = $this->createIndicator($output);
        $indicator->start('Server is running');

        while (true) {
            $stdoutContent = $server->getStdOutContents();
            $output->write($stdoutContent, false, OutputInterface::OUTPUT_RAW | OutputInterface::VERBOSITY_VERBOSE);

            $stderrContents = $server->getStdErrContents();
            $output->write($stderrContents, false, OutputInterface::OUTPUT_RAW | OutputInterface::VERBOSITY_VERBOSE);

            if (!$server->isRunning()) {
                break;
            }

            usleep(200_000); // 0.2 sec
            $indicator->advance();
        }
        $indicator->finish('Server stopped');

        return $server->stop();
    }

    private function printStartMessage(OutputInterface $output, string $path, string $host, int $port): void
    {
        $url = sprintf('http://%s:%d', $host, $port);
        $output->writeln(' Stasis Development Server');
        $output->writeln('');
        $output->writeln(sprintf(' Address:      <href=%s><fg=cyan;options=underscore>%s</></>', $url, $url));
        $output->writeln(sprintf(' Distribution: <fg=yellow>%s</>', $path));
        $output->writeln('');
    }

    private function createIndicator(OutputInterface $output): ProgressIndicator
    {
        $progressOutput = $output->isVerbose() ? new NullOutput() : $output;

        return new ProgressIndicator(
            $progressOutput,
            'normal',
            200,
            [
                '<fg=green>⠏</>',
                '<fg=green>⠛</>',
                '<fg=green>⠹</>',
                '<fg=green>⢸</>',
                '<fg=green>⣰</>',
                '<fg=green>⣤</>',
                '<fg=green>⣆</>',
                '<fg=green>⡇</>',
            ],
            '<fg=red>⠿</>',
        );
    }
}
