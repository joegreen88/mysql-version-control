<?php

namespace Smrtr\MysqlVersionControl\Receiver;

use Smrtr\MysqlVersionControl\DbConfig;
use Symfony\Component\Console\Output\OutputInterface;

class Teardown
{
    public function execute(
        OutputInterface $output,
        $env,
        $confirm = false
    ) {
        $con = DbConfig::getPDO($env, true);
        $stmt = $con->query("SHOW TABLES");
        $result = $stmt->fetchAll();

        $tables = array();
        foreach ($result as $row) {
            $tables[] = array_shift($row);
        }


        if (!$confirm) {
            $dialogue = $this->getHelperSet()->get('dialog');
            $tableCount = count($tables);
            $answer = $dialogue->askConfirmation(
                $output,
                "<question>Tear down $tableCount tables(y/n)?</question>",
                false
            );
            if (!$answer) {
                return 0;
            }
        }

        foreach ($tables as $table) {
            $con->exec("DROP TABLE IF EXISTS `$table`");
            $output->writeln("Dropped table $table");
        }

        return 0;
    }
}