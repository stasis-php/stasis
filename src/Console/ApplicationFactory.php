<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Console;

use Composer\InstalledVersions;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputOption;
use Vstelmakh\Stasis\Command\GenerateCommand;
use Vstelmakh\Stasis\Command\ServerCommand;
use Vstelmakh\Stasis\Config\ConfigInterface;
use Vstelmakh\Stasis\Config\ConfigLoader;

class ApplicationFactory
{
    private const string NAME = 'Stasis';
    private const string PACKAGE_NAME = 'vstelmakh/stasis';
    public const string CONFIG_OPTION = '--config';

    public static function create(ConfigInterface $config): Application
    {
        $version = InstalledVersions::getPrettyVersion(self::PACKAGE_NAME);
        $application = new Application(self::NAME, $version);
        self::hideDefaultCommands($application);
        self::defineConfigOption($application);
        self::defineCommands($application, $config);
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

    private static function defineCommands(Application $application, ConfigInterface $config): void
    {
        $application->addCommands([
            new GenerateCommand($config),
            new ServerCommand($config),
        ]);
    }
}
