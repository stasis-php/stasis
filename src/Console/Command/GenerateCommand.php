<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Vstelmakh\Stasis\Config\ConfigInterface;
use Vstelmakh\Stasis\Generator\SiteGenerator;
use Vstelmakh\Stasis\Router\Compiler\CompiledRouteCollection;
use Vstelmakh\Stasis\Router\Compiler\RouteCompiler;
use Vstelmakh\Stasis\ServiceLocator\ServiceLocator;

class GenerateCommand extends Command
{
    public function __construct(
        private ConfigInterface $config,
    ) {
        parent::__construct('generate');
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Generates static site from specified routes')
            ->addOption('symlink', null, InputOption::VALUE_NONE, 'Symlink static files. Helpful during development to avoid copying assets every time they change.')
            ->setHelp('Generates a static site from the given routes. By default, it builds a complete, ready-to-host website. You can also customize the generation process using the available options.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $symlink = $input->getOption('symlink');

        $serviceLocator = $this->createServiceLocator();
        $compiledRoutes = $this->compileRoutes($serviceLocator);
        $this->generateDist($serviceLocator, $compiledRoutes, $symlink);

        $output->writeln('Generation successful');
        return self::SUCCESS;
    }

    private function createServiceLocator(): ServiceLocator
    {
        $container = $this->config->container();
        return new ServiceLocator($container);
    }

    private function compileRoutes(ServiceLocator $serviceLocator): CompiledRouteCollection
    {
        $compiler = new RouteCompiler('/', $serviceLocator);
        $routes = $this->config->routes();
        return $compiler->compile($routes);
    }

    private function generateDist(
        ServiceLocator $serviceLocator,
        CompiledRouteCollection $compiledRoutes,
        bool $symlink,
    ): void {
        $distribution = $this->config->distribution();
        $siteGenerator = new SiteGenerator($serviceLocator, $distribution);
        $siteGenerator->generate($compiledRoutes, $symlink);
    }
}
