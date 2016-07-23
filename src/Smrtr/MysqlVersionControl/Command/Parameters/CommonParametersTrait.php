<?php

namespace Smrtr\MysqlVersionControl\Command\Parameters;

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
    protected function addGlobalOptions()
    {
        return $this
            ->addConfigAdapterOption()
            ->addConfigAdapterParametersOption()
            ->addVersionsPathOption()
            ->addProvisionalVersionOption()
        ;
    }

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
            'mysql-bin',
            InputArgument::OPTIONAL,
            'Where is the MySQL binary located?',
            'mysql'
        );
    }

    /**
     * @return $this
     */
    protected function addConfigAdapterOption()
    {
        return $this->addOption(
            'config-adapter',
            null,
            InputOption::VALUE_REQUIRED,
            'Specify a database configuration adapter to use. Give the unqualified class name of a shipped adapter, '.
            'or the fully qualified class name of your own custom adapter.'
        );
    }

    /**
     * @return $this
     */
    protected function addConfigAdapterParametersOption()
    {
        return $this->addOption(
            'config-adapter-param',
            null,
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY ,
            'An array of parameters to pass to the constructor function of the database configuration adapter',
            []
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

    /**
     * @return $this
     */
    protected function addProvisionalVersionOption()
    {
        return $this->addOption(
            'provisional-version',
            null,
            InputOption::VALUE_REQUIRED,
            'The name of the provisional version'
        );
    }
}
