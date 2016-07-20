<?php

namespace Smrtr\MysqlVersionControl\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Trait CommonParametersTrait can be used to add common parameters to the console commands.
 *
 * This trait should only be used on instances of Symfony\Component\Console\Command\Command.
 * 
 * @package Smrtr\MysqlVersionControl\Command
 * @author Joe Green <joe.green@smrtr.co.uk>
 */
trait CommonParametersTrait
{
    /**
     * @return $this
     */
    protected function addEnvironmentArgument()
    {
        return $this->addArgument(
            'env',
            InputArgument::REQUIRED,
            "The name of the enviornment to perform database operations on"
        );
    }

    /**
     * @return $this
     */
    protected function addMysqlBinArgument()
    {
        return $this->addArgument(
            'mysqlbin',
            InputArgument::OPTIONAL,
            'Where is the MySQL binary located?'
        );
    }

    /**
     * @return $this
     */
    protected function addVersionsPathOption()
    {
        return $this->addOption(
            'versions-path',
            'p',
            InputOption::VALUE_REQUIRED,
            'Optional custom path to database versions directory'
        );
    }
}