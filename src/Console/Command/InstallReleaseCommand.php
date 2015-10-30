<?php

namespace Accompli\Console\Command;

use Accompli\Accompli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * InstallReleaseCommand.
 *
 * @author  Niels Nijens <nijens.niels@gmail.com>
 */
class InstallReleaseCommand extends Command
{
    /**
     * Configures this command.
     */
    protected function configure()
    {
        $this
            ->setName('install-release')
            ->setDescription('Installs a new release for deployment.')
            ->addArgument('version', InputArgument::REQUIRED, 'The version to install a release for.')
            ->addArgument('stage', InputArgument::OPTIONAL, 'The stage to select hosts for.')
            ->addOption('project-dir', null, InputOption::VALUE_OPTIONAL, 'The location of the project directory.', getcwd());
    }

    /**
     * Executes this command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $parameters = new ParameterBag();
        $parameters->set('configuration.file', $input->getOption('project-dir').DIRECTORY_SEPARATOR.'accompli.json');

        $accompli = new Accompli($parameters);
        $accompli->initialize();

        $configuration = $accompli->getConfiguration();
        $hosts = $configuration->getHosts();
        if ($input->getArgument('stage') !== null) {
            $hosts = $configuration->getHostsByStage($input->getArgument('stage'));
        }

        foreach ($hosts as $host) {
            $accompli->installRelease($host);
        }
    }
}
