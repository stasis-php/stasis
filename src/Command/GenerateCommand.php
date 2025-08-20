<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $serviceLocator = $this->createServiceLocator();
        $compiledRoutes = $this->compileRoutes($serviceLocator);
        $this->generateDist($serviceLocator, $compiledRoutes);

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
        $prefix = $this->config->prefix();
        $compiler = new RouteCompiler('/', $prefix, $serviceLocator);
        $routes = $this->config->routes();
        return $compiler->compile($routes);
    }

    private function generateDist(ServiceLocator $serviceLocator, CompiledRouteCollection $compiledRoutes): void
    {
        $distribution = $this->config->distribution();
        $siteGenerator = new SiteGenerator($serviceLocator, $distribution);
        $siteGenerator->generate($compiledRoutes);
    }
}
