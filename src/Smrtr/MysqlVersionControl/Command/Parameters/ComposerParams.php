<?php

namespace Smrtr\MysqlVersionControl\Command\Parameters;

use Smrtr\MysqlVersionControlException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Class ComposerParams looks for custom command parameters in the composer.json of the project.
 *
 * {
 *   "extra": {
 *     "mysql-version-control": {
 *       "cli": {
 *         "--provisional-version": "new-version"
 *         "--versions-path": "db/commits"
 *       },
 *       "env": {
 *         "development": {
 *           "mysql-bin": "/path/to/mysql/bin"
 *         }
 *       }
 *     }
 *   }
 * }
 *
 * @package Smrtr\MysqlVersionControl
 * @author Joe Green <joe.green@smrtr.co.uk>
 */
class ComposerParams
{
    /**
     * Applies parameters that are defined in the extra section of composer.json.
     *
     * If the Input object already has a value for the argument or parameter then the value in composer is ignored.
     *
     * @param Command $command
     * @param InputInterface $input
     *
     * @return $this
     * @throws MysqlVersionControlException
     */
    public function applyComposerParams(Command $command, InputInterface $input)
    {
        $params = $this->getComposerParams($input->getArgument("env"));
        $definition = $command->getDefinition();

        foreach ($this->filterComposerParams($params, $definition) as $param => $value) {

            if (0 === strpos($param, "--")) { // option

                $option = substr($param, 2);
                $Option = $definition->getOption($option);

                if (!$Option->acceptValue() && false === $input->getOption($option)) {
                    $input->setOption($option, null);
                } elseif ($Option->acceptValue() && $Option->getDefault() === $input->getOption($option)) {
                    if ($Option->isArray()) {
                        $input->setOption($option, is_array($value) ? $value : [$value]);
                    } elseif (is_array($value)) {
                        throw new MysqlVersionControlException(
                            "The '$option' option does not accept arrays. Check your composer.json"
                        );
                    } else {
                        $input->setOption($option, $value);
                    }
                }

            } else { // argument
                $argument = $definition->getArgument($param);
                if ($argument->getDefault() === $input->getArgument($param)) {
                    $input->setArgument($param, $value);
                }
            }
        }

        return $this;
    }

    /**
     * @param string $env
     * @param string|null $composerJsonFilePath
     *
     * @return array
     */
    protected function getComposerParams($env, $composerJsonFilePath = null)
    {
        if (null === $composerJsonFilePath) {
            $composerJsonFilePath = realpath(__DIR__.'/../../../../../../../../composer.json');
        }

        if (!is_file($composerJsonFilePath) or !is_readable($composerJsonFilePath)) {
            return [];
        }

        $parsedJson = json_decode(file_get_contents($composerJsonFilePath), true);

        if (
            !isset($parsedJson["extra"]["mysql-version-control"]) or
            !is_array($parsedJson["extra"]["mysql-version-control"])
        ) {
            return [];
        }

        $smyverJsonParsed = $parsedJson["extra"]["mysql-version-control"];
        $cliParams = $this->getCliComposerParams($smyverJsonParsed);
        $envParams = $this->getEnvironmentComposerParams($env, $smyverJsonParsed);

        return array_merge($cliParams, $envParams);
    }

    /**
     * @param array $smyverJsonParsed
     *
     * @return array
     */
    protected function getCliComposerParams(array $smyverJsonParsed)
    {
        if (!isset($smyverJsonParsed["cli"]) or !is_array($smyverJsonParsed["cli"])) {
            return [];
        }

        return $smyverJsonParsed["cli"];
    }

    /**
     * @param string $env
     * @param array $smyverJsonParsed
     *
     * @return array
     */
    protected function getEnvironmentComposerParams($env, array $smyverJsonParsed)
    {
        if (!isset($smyverJsonParsed["env"]) or !is_array($smyverJsonParsed["env"])) {
            return [];
        }

        $return = [];
        foreach ($smyverJsonParsed["env"] as $environment => $params) {
            $environments = preg_split("/,[\\s]*/", $environment);
            if (in_array($env, $environments)) {
                $return = array_merge($return, $params);
            }
        }

        return $return;
    }

    /**
     * @param array $params
     * @param InputDefinition $definition
     *
     * @return array
     */
    protected function filterComposerParams(array $params, InputDefinition $definition)
    {
        foreach ($params as $param => $value) {
            if (0 === strpos($param, "--")) {
                if (!$definition->hasOption(substr($param, 2))) {
                    unset($params[$param]);
                }
            } else {
                if (!$definition->hasArgument($param)) {
                    unset($params[$param]);
                }
            }
        }
        return $params;
    }
}