<?php

namespace Smrtr\MysqlVersionControl\Command;

use Smrtr\MysqlVersionControl\Command\Parameters\CommonParametersTrait;
use Smrtr\MysqlVersionControl\Command\Parameters\ComposerParams;
use Smrtr\MysqlVersionControl\Receiver\Up as UpReceiver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Up is a symfony console command that updates the database to the latest version.
 * 
 * @package Smrtr\MysqlVersionControl\Command
 * @author Joe Green <joe.green@smrtr.co.uk>
 */
class Up extends Command
{
    use CommonParametersTrait;

    /**
     * @var string
     */
    const DEFAULT_PROVISIONAL_VERSION_NAME = 'new';

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        // Parameters
        $this
            ->addEnvironmentArgument()
            ->addMysqlBinArgument()
            ->addVersionsPathOption()
            ->addOption(
                'no-schema',
                null,
                InputOption::VALUE_NONE,
                'Skip execution of the schema files'
            )
            ->addOption(
                'install-provisional-version',
                null,
                InputOption::VALUE_NONE,
                'Install a provisional version which may still be in development and is not final.'
            )
            ->addOption(
                'provisional-version',
                null,
                InputOption::VALUE_REQUIRED,
                'The name of the provisional version',
                static::DEFAULT_PROVISIONAL_VERSION_NAME
            )
        ;

        // Name & description
        $this
            ->setName("up")
            ->setDescription('Install the latest database versions on the given environment')
        ;
    }

    /**
     * Pass parameters to the receiver and execute.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $composerParams = new ComposerParams;
        $composerParams->applyComposerParams($this, $input);

        $receiver = new UpReceiver;
        return $receiver->execute(
            $input,
            $output,
            $input->getArgument('env'),
            $input->getArgument('mysql-bin'),
            $input->getOption('versions-path'),
            $input->getOption('no-schema'),
            $input->getOption('install-provisional-version'),
            $input->getOption('provisional-version')
        );
    }
}
