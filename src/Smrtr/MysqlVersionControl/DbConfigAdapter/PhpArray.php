<?php

namespace Smrtr\MysqlVersionControl\DbConfigAdapter;

use Smrtr\MysqlVersionControlException;

/**
 * Class PhpArray is an adapter that lets you load database config from a php array.
 *
 * The array must look like the following:
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
 * Pass the array in via the constructor or after construction using the method setArray().
 *
 * @package Smrtr\MysqlVersionControl\DbConfigAdapter
 * @author Joe Green <joe.green@smrtr.co.uk>
 */
class PhpArray implements ConfigAdapterInterface
{
    /**
     * @var array|null This array holds the entire database configuration data
     */
    protected $array;

    /**
     * PhpArray constructor.
     *
     * @param array|null $array Optional array of config to inject right away.
     */
    public function __construct(array $array = null)
    {
        if ($array) {
            $this->setArray($array);
        }
    }

    /**
     * Inject the entire configuration array into the adapter.
     *
     * @param array $array
     *
     * @return $this
     */
    public function setArray(array $array)
    {
        $this->array = $array;
        return $this;
    }

    /**
     * Get the full array of database configurations.
     *
     * @return array
     *
     * @throws MysqlVersionControlException If no configuration is loaded.
     */
    public function getArray()
    {
        if (!is_array($this->array)) {
            throw new MysqlVersionControlException("No configuration is loaded into the PhpArray adapter");
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
                "The PhpArray adapter configuration is malformed; key 'environments' not found or not an array"
            );
        }
        $conf = $conf['environments'];
        if (!isset($conf['environments']) or !is_array($conf['environments'])) {
            throw new MysqlVersionControlException(
                "The PhpArray adapter configuration is malformed; key 'environments['environments']' not found or not an array"
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
                "The PhpArray adapter configuration is malformed; key 'environments' not found or not an array"
            );
        }
        $conf = $conf['environments'];
        if (!isset($conf['testing_environments']) or !is_array($conf['testing_environments'])) {
            throw new MysqlVersionControlException(
                "The PhpArray adapter configuration is malformed; key 'environments['testing_environments']' not found or not an array"
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
                "No configuration was found in the PhpArray adapter for environment '$env'"
            );
        }
        return $conf[$env];
    }
}
