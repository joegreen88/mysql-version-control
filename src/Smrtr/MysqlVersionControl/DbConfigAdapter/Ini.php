<?php

namespace Smrtr\MysqlVersionControl\DbConfigAdapter;

use Smrtr\MysqlVersionControlException;

/**
 * Class Ini is an adapter that loads database configuration from an ini file.
 *
 * You can supply a custom file path by calling setConfigFile,
 * but by default it will load from <project_path>/db/db.ini.
 *
 * An example db.ini:
 *
 * ```
 * [environments]
 * environments[] = "development"
 * environments[] = "production"
 * 
 * testing_environments[] = "development"
 * 
 * [development]
 * runtime.host = "localhost"
 * runtime.user = "buzz"
 * runtime.password = "lightyear"
 * runtime.database = "buzz"
 * runtime.port = 3306
 *
 * buildtime.host = "localhost"
 * buildtime.user = "buzz"
 * buildtime.password = "lightyear"
 * buildtime.database = "buzz"
 * buildtime.port = 3306
 * 
 * [production]
 * runtime.host = "localhost"
 * runtime.user = "root"
 * runtime.password = "root"
 * runtime.database = "buzz"
 * runtime.port = 3306
 * 
 * buildtime.host = "localhost"
 * buildtime.user = "buzz"
 * buildtime.password = "lightyear"
 * buildtime.database = "buzz"
 * buildtime.port = 3306
 * ```
 *
 * @package Smrtr\MysqlVersionControl\DbConfigAdapter
 * @author Joe Green <joe.green@smrtr.co.uk>
 */
class Ini implements ConfigAdapterInterface
{
    /**
     * @var string The template for the file path of the default config file.
     */
    const DEFAULT_CONFIG_FILE_TPL = '%s/db/db.ini';

    /**
     * @var string|null The actual config file to load.
     */
    protected $configFile;

    /**
     * Ini constructor.
     *
     * @param string|null $path
     */
    public function __construct($path = null)
    {
        if ($path) {
            $this->setConfigFile($path);
        }
    }

    /**
     * Set the file path of the config file to load.
     *
     * @param string $path
     *
     * @return $this
     */
    public function setConfigFile($path)
    {
        $this->configFile = (string) $path;
        return $this;
    }

    /**
     * Get the file path of the config file.
     *
     * @return string
     *
     * @throws MysqlVersionControlException If the file path is not readable
     */
    public function getConfigFile()
    {
        if (null === $this->configFile) {
            $this->configFile = sprintf(static::DEFAULT_CONFIG_FILE_TPL, $this->getProjectPath());
        }
        if (!is_readable($this->configFile)) {
            throw new MysqlVersionControlException(
                "Cannot find or open database config; looked in '{$this->configFile}'"
            );
        }
        return $this->configFile;
    }

    /**
     * @inheritDoc
     */
    public function getEnvironments()
    {
        $config = new \Zend_Config_Ini($this->getConfigFile(), 'environments');
        $config = $config->toArray();
        return $config['environments'];
    }

    /**
     * @inheritDoc
     */
    public function getTestingEnvironments()
    {
        $config = new \Zend_Config_Ini(self::getConfigFile(), 'environments');
        $config = $config->toArray();
        return $config['testing_environments'];
    }

    /**
     * @inheritDoc
     */
    public function getConfig($env)
    {
        $config = new \Zend_Config_Ini(self::getConfigFile(), $env);
        return $config->toArray();
    }

    /**
     * @return string The project root path.
     */
    protected function getProjectPath()
    {
        $parts = explode('vendor', __FILE__);
        array_pop($parts);
        return rtrim(implode('vendor', $parts), '/\\');
    }
}
