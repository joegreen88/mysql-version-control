<?php

namespace Smrtr\MysqlVersionControl;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * Class UpCommand
 * @package Smrtr\MysqlVersionControl
 * @author Joe Green
 */
class UpCommand extends Command
{
    /**
     * @var string
     */
    const DEFAULT_PROVISIONAL_VERSION_NAME = 'new';

    /**
     * @var string
     */
    protected $env;

    public function __construct($env)
    {
        $this->env = $env;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName($this->env)
            ->setDescription('Install the '.$this->env.' versions')
            ->addArgument(
                'mysqlbin',
                InputArgument::OPTIONAL,
                'Where is the MySQL binary located?'
            )
            ->addOption(
                'no-schema',
                null,
                InputOption::VALUE_NONE,
                'Skip execution of the schema files'
            )
            ->addOption(
                'versions-path',
                'p',
                InputOption::VALUE_REQUIRED,
                'Optional custom path to database versions directory'
            )
            ->addOption(
                'install-provisional-version',
                null,
                InputOption::VALUE_OPTIONAL,
                'Install a provisional version which may still be in development and is not final. '.PHP_EOL.
                'The provisional version is in a dir named \''.self::DEFAULT_PROVISIONAL_VERSION_NAME.'\' by default; '.
                PHP_EOL.'provide a value for this option to override the dirname.'
            )
        ;
    }

    /**
     * Load a few settings then run the installer.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $mysqlbin = $input->getArgument('mysqlbin') ?: 'mysql';
        $buildConf = DbConfig::getPDO($this->env, true);
        $runConf = DbConfig::getPDO($this->env);

        // 1. Make sure that db_config table is present

        $output->writeln('');
        $output->writeln('Checking database status... ');
        $output->writeln('');


        if (!$buildConf instanceof \PDO) {
            $output->writeln('<error>Failed: unable to obtain a database connection.</error>');
            return 1;
        }

        if ($buildConf->query("SHOW TABLES LIKE 'db_config'")->rowCount()) {

            $output->writeln('<info>Database version control is already installed.</info>');

        } else {

            $output->writeln('Installing version control...');

            $result = $buildConf->prepare(
"CREATE TABLE `db_config`
(
    `key` VARCHAR(50) COLLATE 'utf8_general_ci' NOT NULL,
    `value` TEXT,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`key`),
    UNIQUE INDEX `db_config_U_1` (`key`)
) ENGINE=MyISAM;"
            )->execute();

            if (!$result) {
                $output->writeln('<error>Installing version control failed.</error>');
                return 1;
            }

            $output->writeln('<info>Installed version control successfully.</info>');
        }

        // 2. Check for current version and available version

        // what is the versions path?
        if ($input->getOption('versions-path')) {
            $versionsPath = $input->getOption('versions-path');
        } else {
            $versionsPath = realpath(dirname(__FILE__).'/../../../../../../db/versions');
        }
        if (!is_readable($versionsPath)) {
            $output->writeln('<error>Versions path is not readable: '.$versionsPath.'</error>');
            return 1;
        }
        if (!is_dir($versionsPath)) {
            $output->writeln('<error>Versions path is not a directory: '.$versionsPath.'</error>');
            return 1;
        }

        // what is the current version?
        $query = $runConf->query("SELECT `value` FROM `db_config` WHERE `key`='version'");
        if ($query->rowCount()) {
            $versionRow = $query->fetch(\PDO::FETCH_ASSOC);
            $currentVersion = (int) $versionRow['value'];
        } else {
            $currentVersion = 0;
        }
        $output->writeln('Current version: '.$currentVersion);

        // what is the available version?
        $availableVersion = 0;
        foreach (scandir($versionsPath) as $path) {
            if (preg_match("/^(\\d)+$/", $path) && (int) $path > $availableVersion) {
                $availableVersion = (int) $path;
            }
        }
        $output->writeln('Available version: '.$availableVersion);

        if ($currentVersion >= $availableVersion) {
            $output->writeln('<info>Database version is already up to date.</info>');
            return 0;
        }

        $noun = ($availableVersion - $currentVersion > 1) ? 'updates' : 'update';
        $output->writeln(
            "Installing database $noun (Current version: $currentVersion, Available version: $availableVersion)..."
        );

        // go from current to latest version, building stack of SQL files
        $filesToLookFor = [];
        if (!$input->getOption('no-schema')) {
            $filesToLookFor[] = 'schema.sql';       // structural changes, alters, creates, drops
        }
        $filesToLookFor[] = 'data.sql';             // core data, inserts, replaces, updates, deletes
        if (in_array($this->env, DbConfig::getTestingEnvironments())) {
            $filesToLookFor[] = 'testing.sql';      // extra data on top of data.sql for the testing environment(s)
        }
        $filesToLookFor[] = 'runme.php';            // custom php hook

        $stack = array();
        for ($i = $currentVersion + 1; $i <= $availableVersion; $i++) {

            $path = $versionsPath.DIRECTORY_SEPARATOR.$i;
            if (!is_dir($path) || !is_readable($path)) {
                continue;
            }

            foreach ($filesToLookFor as $file) {
                if (is_readable($path.DIRECTORY_SEPARATOR.$file) && is_file($path.DIRECTORY_SEPARATOR.$file)) {
                    $stack[$i][$file] = $path.DIRECTORY_SEPARATOR.$file;
                }
            }
        }

        // Look for a provisional version?

        if (($provisionalVersion = $input->getOption('install-provisional-version'))) {

            if (!is_string($provisionalVersion)) {
                $provisionalVersion = self::DEFAULT_PROVISIONAL_VERSION_NAME;
            }

            $path = $versionsPath.DIRECTORY_SEPARATOR.$provisionalVersion;
            if (is_readable($path) && is_dir($path)) {

                foreach ($filesToLookFor as $file) {
                    if (is_readable($file.DIRECTORY_SEPARATOR.$file) && is_file($path.DIRECTORY_SEPARATOR.$file)) {
                        $stack[$provisionalVersion][$file] = $path.DIRECTORY_SEPARATOR.$file;
                    }
                }
            }
        }

        $s = '\\' == DIRECTORY_SEPARATOR ? "%s" : "'%s'"; // Windows doesn't like quoted params
        $cmdMySQL = "$mysqlbin -h $s --user=$s --password=$s --database=$s < %s";

        // loop sql file stack and execute on mysql CLI

        $dbConf = DbConfig::getConfig($this->env);

        $previousVersion = $currentVersion;
        $result = true;
        foreach ($stack as $version => $files) {

            $output->write($previousVersion." -> $version ");

            if (!$result) {
                $output->write('skipped');
                continue;
            }

            foreach ($files as $file) {

                if ('schema.sql' === $file) {
                    $conf = $dbConf['buildtime'];
                } else {
                    $conf = $dbConf['runtime'];
                }
                $host = $conf['host'];
                $user = $conf['user'];
                $pass = $conf['password'];
                $name = $conf['database'];

                if ('.sql' === substr($file, -4)) {

                    $command = sprintf(
                        $cmdMySQL,
                        $host,
                        $user,
                        $pass,
                        $name,
                        $file
                    );

                    $process = new Process($command);
                    $process->run();

                    if (!$process->isSuccessful()) {
                        $result = false;
                        break;
                    }

                    continue;
                }

                if ('.php' === substr($file, -4)) {

                    $feedback = require_once $file;
                }
            }

            if ($result) {
                $result = $buildConf->query(
                    "REPLACE INTO `db_config` (`key`, `value`, `updated_at`) VALUES ('version', $version, now())"
                )->execute();
            }

            $statusMsg = $result ? '<info>OK</info>' : '<error>Failed</error>';
            $output->write($statusMsg, true);

            if (isset($feedback) && is_string($feedback) && strlen($feedback)) {
                $output->write($feedback);
                unset($feedback);
            }

            if (!$result) {
                $output->write('<error>'.$process->getErrorOutput().'</error>');
            }

            $previousVersion = $version;
        }

        if ($result) {
            $output->writeln('<info>Database updates installed successfully.</info>');
            return 0;

        } else {
            $output->writeln('<error>Installing database updates failed.</error>');
            return 1;
        }
    }
}
