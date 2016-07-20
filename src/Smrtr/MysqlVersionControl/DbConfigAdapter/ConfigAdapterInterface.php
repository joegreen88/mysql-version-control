<?php

namespace Smrtr\MysqlVersionControl\DbConfigAdapter;

/**
 * Interface DbConfigAdapterInterface defines the requirements for implementing a db config adapter.
 *
 * Db config adapters are used to load database configuration from your preferred source.
 * 
 * @package Smrtr\MysqlVersionControl\DbConfigAdapter
 * @author Joe Green <joe.green@smrtr.co.uk>
 */
interface ConfigAdapterInterface
{
    /**
     * Get a list of all environments.
     *
     * @return string[] A list of all environment names
     */
    public function getEnvironments();

    /**
     * Get a list of testing environments.
     *
     * @return string[] A list of environment names which are testing environments
     */
    public function getTestingEnvironments();

    /**
     * Get the database configurations for the given environment.
     *
     * Returns an array with keys 'buildtime' and 'runtime',
     * where both elements are themselves arrays of database config with keys 'host', 'database', 'user', 'password'.
     *
     * @param string $env The name of the environment
     *
     * @return array
     */
    public function getConfig($env);
}