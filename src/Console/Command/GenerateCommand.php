<?php

declare(strict_types=1);

namespace Stasis\Console\Command;

use Stasis\Console\CommandFactoryInterface;
use Stasis\Generator\SiteGenerator;
use Stasis\Kernel;
use Stasis\Router\Compiler\RouteCompiler;
use Stasis\ServiceLocator\ServiceLocator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends Command implements CommandFactoryInterface
{
    private const string NAME = 'generate';
    private const string DESCRIPTION = 'Generates static site from specified routes';

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
        $container = $kernel->container();
        $serviceLocator =  new ServiceLocator($container);

        $distribution = $kernel->distribution();
        $siteGenerator = new SiteGenerator($serviceLocator, $distribution);

        $compiler = new RouteCompiler('/', $serviceLocator);
        $routes = $kernel->routes();

        return new self($siteGenerator, $compiler, $routes);
    }

    public function __construct(
        private readonly SiteGenerator $siteGenerator,
        private readonly RouteCompiler $routeCompiler,
        private readonly iterable $routes,
    ) {
        parent::__construct(self::NAME);
    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::DESCRIPTION)
            ->addOption('symlink', null, InputOption::VALUE_NONE, 'Symlink static files. Helpful during development to avoid copying assets every time they change.')
            ->setHelp('Generates a static site from the given routes. By default, it builds a complete, ready-to-host website. You can also customize the generation process using the available options.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $symlink = $input->getOption('symlink');

        $compiledRoutes = $this->routeCompiler->compile($this->routes);
        $this->siteGenerator->generate($compiledRoutes, $symlink);

        $output->writeln('Generation successful');
        return self::SUCCESS;
    }
}
