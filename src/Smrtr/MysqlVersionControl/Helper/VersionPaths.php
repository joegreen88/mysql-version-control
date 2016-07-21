<?php

namespace Smrtr\MysqlVersionControl\Helper;

use Smrtr\MysqlVersionControlException;

/**
 * Class VersionPaths has some sttic methods for helping us out with version pathing.
 *
 * I've put these methods in this class so they can be called from multiple commands in a consistent way.
 *
 * @package Smrtr\MysqlVersionControl\Helper
 * @author Joe Green <joe.green@smrtr.co.uk>
 */
class VersionPaths
{
    /**
     * @var string The template for the default versions path
     */
    const DEFAULT_VERSIONS_PATH_TPL = "%s/db/versions";

    /**
     * @var string The template for the default provisional version path
     */
    const DEFAULT_PROVISIONAL_VERSION_PATH_TPL = "%s/new";

    /**
     * This method takes a versions path that could be relative or absolute and returns an absolute path.
     *
     * It is safe to run a versions path through this method multiple times, it will return the same path each time.
     *
     * @param string|null $versionsPath
     *
     * @return string Always returns an absolute path
     *
     * @throws MysqlVersionControlException
     */
    public static function resolveVersionsPath($versionsPath)
    {
        // what is the versions path?
        $projectPath = realpath(dirname(__FILE__).'/../../../../../../..');
        if (!$versionsPath) {
            $versionsPath = sprintf(static::DEFAULT_VERSIONS_PATH_TPL, $projectPath);
        }
        if (!in_array(substr($versionsPath, 0, 1), ["/", "\\"])) { // then we assume path is relative to project root
            $versionsPath = "$projectPath/$versionsPath";
        }
        if (!is_readable($versionsPath)) {
            throw new MysqlVersionControlException("Versions path is not readable: '$versionsPath'");
        }
        if (!is_dir($versionsPath)) {
            throw new MysqlVersionControlException("Versions path is not a directory: '$versionsPath'");
        }
        return $versionsPath;
    }

    /**
     * This method takes a version path and a provisional version name and returns a full provisional version path.
     *
     * @param string|null $versionsPath
     * @param string|null $provisionalVersionPath
     *
     * @return string Always returns an absolute path
     */
    public static function resolveProvisionalVersionPath($versionsPath, $provisionalVersionPath)
    {
        if (!$provisionalVersionPath) {
            return sprintf(static::DEFAULT_PROVISIONAL_VERSION_PATH_TPL, static::resolveVersionsPath($versionsPath));
        }
        return static::resolveVersionsPath($versionsPath)."/".ltrim($provisionalVersionPath, "/\\");
    }

    /**
     * Return a list of files to look for based on the given parameters.
     *
     * @param bool $includeSchema
     * @param bool $includeTesting
     *
     * @return array
     */
    public static function getVersioningFilesToLookFor($includeSchema = true, $includeTesting = true)
    {
        $filesToLookFor = [];
        if ($includeSchema) {
            $filesToLookFor[] = 'schema.sql';       // structural changes, alters, creates, drops
        }
        $filesToLookFor[] = 'data.sql';             // core data, inserts, replaces, updates, deletes
        if ($includeTesting) {
            $filesToLookFor[] = 'testing.sql';      // extra data on top of data.sql for the testing environment(s)
        }
        $filesToLookFor[] = 'runme.php';            // custom php hook
        return $filesToLookFor;
    }
}