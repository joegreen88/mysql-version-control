<?php

namespace Smrtr\MysqlVersionControl\DbConfigAdapter;
use Smrtr\MysqlVersionControlException;

/**
 * Class PhpFile is an adapter that lets you load database config from a php file.
 *
 * The php file can contain any logic but it must return an array like the following:
 *
 * ```
 * [
 *   'environments' => [
 *     'environments' => ['A', 'B'],
 *     'testing_environments' => ['B'],
 *   ],
 *   'A' => [
 *     'buildtime' => [
 *       'host' => '',
 *       'database' => '',
 *       'user' => '',
 *       'password' => '',
 *       'port' => '',
 *     ],
 *     'runtime' => [
 *       'host' => '',
 *       'database' => '',
 *       'user' => '',
 *       'password' => '',
 *       'port' => '',
 *     ],
 *   ],
 *   'B' => [
 *     'buildtime' => [
 *       'host' => '',
 *       'database' => '',
 *       'user' => '',
 *       'password' => '',
 *       'port' => '',
 *     ],
 *     'runtime' => [
 *       'host' => '',
 *       'database' => '',
 *       'user' => '',
 *       'password' => '',
 *       'port' => '',
 *     ],
 *   ],
 * ]
 * ```
 * where A, B are the names of the available environments.
 *
 * Pass the file path in via the constructor or after construction using the method setFilePath().
 *
 * @package Smrtr\MysqlVersionControl\DbConfigAdapter
 * @author Joe Green <joe.green@smrtr.co.uk>
 */
class PhpFile implements ConfigAdapterInterface
{
    /**
     * @var array|null This array holds the returned from file database configuration data
     */
    protected $array;

    /**
     * @var string|null The path to the php file
     */
    protected $filePath;

    /**
     * PhpFile constructor.
     *
     * @param string|null $phpFilePath Optional path to the php file
     */
    public function __construct($phpFilePath = null)
    {
        if ($phpFilePath) {
            $this->setFilePath($phpFilePath);
        }
    }

    /**
     * Set the path to the php file and invalidate any previously loaded configuration.
     *
     * If the path provided is not absolute then it is assumed to be relative to the project root.
     *
     * @param string $phpFilePath The path to the php file
     *
     * @return $this
     */
    public function setFilePath($phpFilePath)
    {
        if (!in_array(substr($phpFilePath, 0, 1), ["/", "\\"])) { // then we assume path is relative to project root
            $projectPath = realpath(dirname(__FILE__).'/../../../../../../..');
            $phpFilePath = "$projectPath/$phpFilePath";
        }
        $this->filePath = $phpFilePath;
        $this->array = null;
        return $this;
    }

    /**
     * @return null|string The configured file path to the php file that will return the database configurations.
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * Get the full array of database configurations.
     *
     * @return array
     *
     * @throws MysqlVersionControlException If no configuration is loaded and no php file is specified.
     */
    public function getArray()
    {
        if (!is_array($this->array)) {
            if (!$this->loadConfig()) {
                throw new MysqlVersionControlException("No configuration is loaded into the PhpFile adapter");
            }
        }
        return $this->array;
    }

    /**
     * @inheritDoc
     *
     * @throws MysqlVersionControlException If the required environments keys are not present in the array
     */
    public function getEnvironments()
    {
        $conf = $this->getArray();
        if (!isset($conf['environments']) or !is_array($conf['environments'])) {
            throw new MysqlVersionControlException(
                "The PhpFile adapter configuration is malformed; key 'environments' not found or not an array"
            );
        }
        $conf = $conf['environments'];
        if (!isset($conf['environments']) or !is_array($conf['environments'])) {
            throw new MysqlVersionControlException(
                "The PhpFile adapter configuration is malformed; key 'environments['environments']' not found or not an array"
            );
        }
        return $conf['environments'];
    }

    /**
     * @inheritDoc
     *
     * @throws MysqlVersionControlException If the required environments keys are not present in the array
     */
    public function getTestingEnvironments()
    {
        $conf = $this->getArray();
        if (!isset($conf['environments']) or !is_array($conf['environments'])) {
            throw new MysqlVersionControlException(
                "The PhpFile adapter configuration is malformed; key 'environments' not found or not an array"
            );
        }
        $conf = $conf['environments'];
        if (!isset($conf['testing_environments']) or !is_array($conf['testing_environments'])) {
            throw new MysqlVersionControlException(
                "The PhpFile adapter configuration is malformed; key 'environments['testing_environments']' not found or not an array"
            );
        }
        return $conf['testing_environments'];
    }

    /**
     * @inheritDoc
     *
     * @throws MysqlVersionControlException If the required environment key is not present in the array
     */
    public function getConfig($env)
    {
        $conf = $this->getArray();
        if (!isset($conf[$env]) or !is_array($conf[$env])) {
            throw new MysqlVersionControlException(
                "No configuration was found in the PhpFile adapter for environment '$env'"
            );
        }
        return $conf[$env];
    }

    /**
     * Attempt to load an array from the file and store it in $this->array.
     *
     * @return bool True if array was loaded from file, false otherwise
     */
    protected function loadConfig()
    {
        $array = include $this->filePath;
        if (is_array($array)) {
            $this->array = $array;
            return true;
        }
        return false;
    }
}
