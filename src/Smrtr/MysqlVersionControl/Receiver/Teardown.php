<?php

namespace Smrtr\MysqlVersionControl\Receiver;

use Smrtr\MysqlVersionControl\DbConfig;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class Teardown
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param string $env
     * @param bool $confirm
     *
     * @return int
     */
    public function execute(
        InputInterface $input,
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
            $tableCount = count($tables);
            $questionHelper = new QuestionHelper;
            $question = new ConfirmationQuestion("<question>Tear down $tableCount tables(y/n)?</question>", false);
            if (!$questionHelper->ask($input, $output, $question)) {
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