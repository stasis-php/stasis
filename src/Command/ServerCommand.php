<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressIndicator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Vstelmakh\Stasis\Config\ConfigInterface;
use Vstelmakh\Stasis\Server\Server;

class ServerCommand extends Command
{
    public function __construct(
        private readonly ConfigInterface $config,
    ) {
        parent::__construct('server');
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Start the development server')
            ->addOption('host', null, InputOption::VALUE_REQUIRED, 'Host to run the server on', 'localhost')
            ->addOption('port', null, InputOption::VALUE_REQUIRED, 'Port to run the server on', 8000)
            ->setHelp('Stasis development server is utilizing PHP built-in web server. Does NOT meant to be used in production environment.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = $this->config->distribution()->path();
        $host = $input->getOption('host');
        $port = $input->getOption('port');

        $this->printStartMessage($output, $path, $host, $port);

        $server = new Server($path, $host, $port);
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
                '<fg=green>⡇</>'
            ],
            '<fg=red>⠿</>'
        );
    }
}
