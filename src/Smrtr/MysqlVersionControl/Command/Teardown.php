<?php

namespace Smrtr\MysqlVersionControl\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Smrtr\MysqlVersionControl\Receiver\Teardown as TeardownReceiver;

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
     * @var string
     */
    const DEFAULT_PROVISIONAL_VERSION_NAME = 'new';

    /**
     * @var string
     */
    protected $env;

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        // Parameters
        $this
            ->addEnvironmentArgument()
            ->addMysqlBinArgument()
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
        $receiver = new TeardownReceiver();
        return $receiver->execute(
            $output,
            $input->getArgument('env'),
            $input->getOption('confirm')
        );
    }
}
