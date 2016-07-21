<?php

namespace Smrtr\MysqlVersionControl\Helper;

use Smrtr\MysqlVersionControl\DbConfig;
use Smrtr\MysqlVersionControlException;
use Symfony\Component\Console\Input\InputInterface;

class Configuration
{
    /**
     * @var string THe default config adapter to use in case there are custom parameters but no custom adapter defined.
     */
    const DEFAULT_CONFIG_ADAPTER = "Ini";

    /**
     * @var string The namespace where the shipped adapter classes can be found.
     */
    const SMRTR_ADAPTER_NAMESPACE = "\\Smrtr\\MysqlVersionControl\\DbConfigAdapter";

    /**
     * Looks at the input options for the configuration adapter and if applicable applies the necessary adapter.
     *
     * @param InputInterface $input
     *
     * @return null
     * @throws MysqlVersionControlException
     */
    public static function applyConsoleConfigurationOptions(InputInterface $input)
    {
        $configAdapter = (string) $input->getOption("config-adapter");
        $configAdapterParams = (array) $input->getOption("config-adapter-param");

        if (!count($configAdapterParams) && !strlen($configAdapter)) {
            return;
        }

        if (!$configAdapter) {
            $configAdapter = static::DEFAULT_CONFIG_ADAPTER;
        }

        if (false === strpos($configAdapter, "\\")) { // Not a fully qualified class name
            $configAdapter = static::SMRTR_ADAPTER_NAMESPACE."\\$configAdapter";
        }

        if (!class_exists($configAdapter)) {
            throw new MysqlVersionControlException("Unknown class '$configAdapter'");
        }

        $adapter = (new \ReflectionClass($configAdapter))->newInstanceArgs($configAdapterParams);
        DbConfig::setAdapter($adapter);
    }
}