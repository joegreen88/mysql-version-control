<?php

namespace Smrtr\MysqlVersionControl;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UpCommand
 * @package Smrtr\MysqlVersionControl
 * @author Joe Green
 */
class UpCommand extends Command
{
    protected $env;

    public function __construct($env)
    {
        $this->env = $env;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName($this->env)
            ->setDescription('Install the '.$this->env.' versions')
            ->addArgument(
                'mysqlbin',
                InputArgument::OPTIONAL,
                'Where is the MySQL binary located?'
            )
            ->addOption(
                'confirm',
                null,
                InputOption::VALUE_NONE,
                'If set, the command will bypass the confirmation prompt'
            )
        ;
    }

    /**
     * Load a few settings then run the installer.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Controller::update(
            $this->env,
            $output,
            $this->getHelperSet()->get('dialog'),
            (bool) $input->getOption('confirm'),
            $input->getArgument('mysqlbin') ?: 'mysql'
        );
    }
}
