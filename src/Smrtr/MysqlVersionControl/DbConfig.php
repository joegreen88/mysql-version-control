<?php

namespace Smrtr\MysqlVersionControl;

use Smrtr\MysqlVersionControl\DbConfigAdapter\ConfigAdapterInterface;
use Smrtr\MysqlVersionControl\DbConfigAdapter\Ini;

/**
 * Class DbConfig reads database config.
 * 
 * By default it reds from an ini file using the Ini config adapter.
 *
 * Set your own adapter by calling setAdapter to load db config with your own strategy.
 *
 * @package Smrtr\MysqlVersionControl
 * @author Joe Green
 */
class DbConfig
{
    /**
     * @var ConfigAdapterInterface|null
     */
    protected static $adapter;

    /**
     * Set the database configuration adapter.
     *
     * @param ConfigAdapterInterface $adapter
     *
     * @return null
     */
    public static function setAdapter(ConfigAdapterInterface $adapter)
    {
        static::$adapter = $adapter;
        return null;
    }

    /**
     * Get the database configuration adapter.
     *
     * @return ConfigAdapterInterface
     */
    public static function getAdapter()
    {
        if (null === static::$adapter) {
            static::$adapter = new Ini;
        }
        return static::$adapter;
    }

    /**
     * Get a list of all environments.
     *
     * @return string[] A list of all environment names
     */
    public static function getEnvironments()
    {
        return static::getAdapter()->getEnvironments();
    }

    /**
     * Get a list of testing environments.
     *
     * @return string[] A list of environment names which are testing environments
     */
    public static function getTestingEnvironments()
    {
        return static::getAdapter()->getTestingEnvironments();
    }

    /**
     * Get the database configurations for the given environment.
     *
     * Returns an array with keys 'buildtime' and 'runtime',
     * where both elements are themselves arrays of database config
     * with keys 'host', 'database', 'user', 'password', 'port'.
     *
     * @param string $env The name of the environment
     *
     * @return array
     */
    public static function getConfig($env)
    {
        return static::getAdapter()->getConfig($env);
    }

    /**
     * Get a PDO connection object for the given environment.
     *
     * @param string $env
     * @param bool $buildtime Optional; false by default. Set to true to get buildtime config instead of runtime.
     *
     * @return \PDO
     */
    public static function getPDO($env, $buildtime = false)
    {
        $key = $buildtime ? 'buildtime' : 'runtime';
        $config = static::getConfig($env);
        $config = $config[$key];

        $dsn = sprintf("mysql:host=%s", $config["host"]);
        if (isset($config["port"]) && $config["port"]) {
            $dsn .= sprintf(";port=%s", $config["port"]);
        }
        $dsn .= sprintf(";dbname=%s", $config["database"]);

        $db = new \PDO($dsn, $config['user'], $config['password']);

        return $db;
    }
}
