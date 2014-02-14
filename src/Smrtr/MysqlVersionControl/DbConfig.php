<?php

namespace Smrtr\MysqlVersionControl;
use Smrtr\MysqlVersionControlException;

/**
 * Class DbConfig reads database config from an ini file.
 *
 * @package Smrtr\MysqlVersionControl
 * @author Joe Green
 */
class DbConfig
{
    public static function getEnvironments()
    {
        $config = new \Zend_Config_Ini(self::getConfigFile(), 'environments');
        $config = $config->toArray();
        return $config['environments'];
    }

    public static function getTestingEnvironments()
    {
        $config = new \Zend_Config_Ini(self::getConfigFile(), 'environments');
        $config = $config->toArray();
        return $config['testing_environments'];
    }

    public static function getPDO($env, $buildtime = false)
    {
        $key = $buildtime ? 'buildtime' : 'runtime';
        $config = self::getConfig($env);
        $config = $config[$key];

        $dsn = sprintf('mysql:host=%s;dbname=%s', $config['host'], $config['database']);
        $db = new \PDO($dsn, $config['user'], $config['password']);

        return $db;
    }

    public static function getConfig($env)
    {
        $config = new \Zend_Config_Ini(self::getConfigFile(), $env);
        return $config->toArray();
    }

    protected static function getConfigFile()
    {
        $configFile = self::getProjectPath() . '/db/db.ini';

        if (!is_readable($configFile)) {
            throw new MysqlVersionControlException(
                "Cannot find or open database config; looked in '$configFile'"
            );
        }

        return $configFile;
    }

    protected static function getProjectPath()
    {
        $parts = explode('vendor', __FILE__);
        array_pop($parts);
        return rtrim(implode('vendor', $parts), '/\\');
    }
}
