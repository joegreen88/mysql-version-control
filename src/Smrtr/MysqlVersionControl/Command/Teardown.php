<?php

namespace Smrtr\MysqlVersionControl\Command;

use Smrtr\MysqlVersionControl\Command\Parameters\CommonParametersTrait;
use Smrtr\MysqlVersionControl\Command\Parameters\ComposerParams;
use Smrtr\MysqlVersionControl\Helper\Configuration;
use Smrtr\MysqlVersionControl\Receiver\Teardown as TeardownReceiver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Teardown is a symfony console command that removes all tables from the database for the given environment.
 *
 * @package Smrtr\MysqlVersionControl\Command
 * @author Joe Green <joe.green@smrtr.co.uk>
 */
class Teardown extends Command
{
    use CommonParametersTrait;

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        // Parameters
        $this
            ->addEnvironmentArgument()
            ->addGlobalOptions()
            ->addOption(
                'confirm',
                null,
                InputOption::VALUE_NONE,
                'If set, the command will bypass the confirmation prompt'
            )
        ;

        // Name & description
        $this
            ->setName("teardown")
            ->setDescription('Tear down the database tables from the given environment')
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
        Configuration::applyConsoleConfigurationOptions($input);

        $receiver = new TeardownReceiver;
        return $receiver->execute(
            $input,
            $output,
            $input->getArgument('env'),
            $input->getOption('confirm')
        );
    }
}
