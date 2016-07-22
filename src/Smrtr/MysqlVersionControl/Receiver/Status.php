<?php

namespace Smrtr\MysqlVersionControl\Receiver;

use Smrtr\MysqlVersionControl\DbConfig;
use Smrtr\MysqlVersionControl\Helper\VersionPaths;
use Smrtr\MysqlVersionControlException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Status
{
    /**
     * @var \PDO[]
     */
    protected $buildtimeConnections = [];

    /**
     * @var \PDO[]
     */
    protected $runtimeConnections = [];

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param string $env
     * @param string|null $versionsPath
     * @param string|null $provisionalVersion
     *
     * @return int
     */
    public function execute(
        InputInterface $input,
        OutputInterface $output,
        $env,
        $versionsPath = null,
        $provisionalVersion = null
    ) {
        if (!$this->hasVersionControl($env)) {
            $output->writeln("The database is not currently under version control");
        }

        $output->writeln("Current version: ".$this->getCurrentVersion($env));

        $output->writeln("Available version: ".$this->getAvailableVersion($versionsPath));

        if ($this->hasProvisionalVersion($versionsPath, $provisionalVersion)) {
            $output->writeln("A provisional version is also available");
        }

        return 0;
    }

    /**
     * @param string $env
     *
     * @return bool
     */
    public function hasVersionControl($env)
    {
        return (boolean) $this->getBuildtimeConnection($env)->query("SHOW TABLES LIKE 'db_config'")->rowCount();
    }

    /**
     * @param string $env
     *
     * @return int
     */
    public function getCurrentVersion($env)
    {
        $query = $this->getRuntimeConnection($env)->query("SELECT `value` FROM `db_config` WHERE `key`='version'");
        if ($query instanceof \PDOStatement && $query->rowCount()) {
            $versionRow = $query->fetch(\PDO::FETCH_ASSOC);
            $currentVersion = (int) $versionRow['value'];
        } else {
            $currentVersion = 0;
        }
        return $currentVersion;
    }

    /**
     * @param string $versionsPath
     *
     * @return int
     */
    public function getAvailableVersion($versionsPath)
    {
        $availableVersion = 0;
        foreach (scandir(VersionPaths::resolveVersionsPath($versionsPath)) as $path) {
            if (preg_match("/^(\\d)+$/", $path) && (int) $path > $availableVersion) {
                $availableVersion = (int) $path;
            }
        }
        return $availableVersion;
    }

    /**
     * Returns true iff there is a provisional version directory with at least one recognized file inside it.
     *
     * @param string|null $versionsPath
     * @param string|null $provisionalVersion
     *
     * @return bool
     */
    public function hasProvisionalVersion($versionsPath, $provisionalVersion)
    {
        $filesToLookFor = VersionPaths::getVersioningFilesToLookFor(true, true);
        $path = VersionPaths::resolveProvisionalVersionPath($versionsPath, $provisionalVersion);
        if (is_readable($path) && is_dir($path)) {
            foreach ($filesToLookFor as $file) {
                if (is_readable($path.DIRECTORY_SEPARATOR.$file) && is_file($path.DIRECTORY_SEPARATOR.$file)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param string $env
     *
     * @return \PDO
     * @throws MysqlVersionControlException
     */
    protected function getBuildtimeConnection($env)
    {
        if (!array_key_exists($env, $this->buildtimeConnections)) {
            $this->buildtimeConnections[$env] = DbConfig::getPDO($env, true);
            if (! $this->buildtimeConnections[$env] instanceof \PDO) {
                throw new MysqlVersionControlException("Unable to obtain a database connection");
            }
        }
        return $this->buildtimeConnections[$env];
    }

    /**
     * @param string $env
     *
     * @return \PDO
     * @throws MysqlVersionControlException
     */
    protected function getRuntimeConnection($env)
    {
        if (!array_key_exists($env, $this->runtimeConnections)) {
            $this->runtimeConnections[$env] = DbConfig::getPDO($env, false);
            if (! $this->runtimeConnections[$env] instanceof \PDO) {
                throw new MysqlVersionControlException("Unable to obtain a database connection");
            }
        }
        return $this->runtimeConnections[$env];
    }
}