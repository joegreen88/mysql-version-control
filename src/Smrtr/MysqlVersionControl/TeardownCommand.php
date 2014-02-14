<?php

namespace Smrtr\MysqlVersionControl;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TeardownCommand
 * @package Smrtr\MysqlVersionControl
 * @author Joe Green
 */
class TeardownCommand extends Command
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
     * Confirmation prompt then tear down the database tables
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $con = DbConfig::getPDO($this->env, true);
        $stmt = $con->query("SHOW TABLES");
        $result = $stmt->fetchAll();

        $tables = array();
        foreach ($result as $row) {
            $tables[] = array_shift($row);
        }

        $dialogue = $this->getHelperSet()->get('dialog');
        if (!$input->getOption('confirm')) {
            $tableCount = count($tables);
            $answer = $dialogue->askConfirmation(
                $output,
                "<question>Tear down $tableCount tables(y/n)?</question>",
                false
            );
            if (!$answer) {
                exit;
            }
        }

        foreach ($tables as $table) {
            $con->exec("DROP TABLE IF EXISTS `$table`");
            $output->writeln("Dropped table $table");
        }
    }

}
