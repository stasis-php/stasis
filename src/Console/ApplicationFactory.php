<?php

declare(strict_types=1);

namespace Stasis\Console;

use Composer\InstalledVersions;
use Stasis\Config\ConfigLoader;
use Stasis\Console\Command\GenerateCommand;
use Stasis\Console\Command\ServerCommand;
use Stasis\Kernel;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputOption;

class ApplicationFactory
{
    private const string NAME = 'Stasis';
    private const string PACKAGE_NAME = 'stasis/stasis';
    public const string CONFIG_OPTION = '--config';

    public static function create(Kernel $kernel): Application
    {
        $version = InstalledVersions::getPrettyVersion(self::PACKAGE_NAME);
        $application = new Application(self::NAME, $version);
        self::hideDefaultCommands($application);
        self::defineConfigOption($application);
        self::defineCommands($application, $kernel);
        return $application;
    }

    private static function hideDefaultCommands(Application $application): void
    {
        $application->get('help')->setHidden(true);
        $application->get('list')->setHidden(true);
        $application->get('completion')->setHidden(true);
    }

    private static function defineConfigOption(Application $application): void
    {
        $application->getDefinition()->addOption(new InputOption(
            self::CONFIG_OPTION,
            null,
            InputOption::VALUE_REQUIRED,
            'Path to config file (relative to project root or absolute)',
            ConfigLoader::DEFAULT_CONFIG,
        ));
    }

    private static function defineCommands(Application $application, Kernel $kernel): void
    {
        $loader = new CommandLoader($kernel, [
            GenerateCommand::class,
            ServerCommand::class,
        ]);

        $application->setCommandLoader($loader);
    }
}
