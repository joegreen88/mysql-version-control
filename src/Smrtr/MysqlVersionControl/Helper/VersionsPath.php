<?php

namespace Smrtr\MysqlVersionControl\Helper;

use Smrtr\MysqlVersionControlException;

class VersionsPath
{
    /**
     * @var string The template for the default versions path
     */
    const DEFAULT_VERSIONS_PATH_TPL = "%s/db/versions";

    /**
     * THis method takes a versions path that could be relative or absolute and returns an absolute path.
     *
     * It is safe to run a versions path through this method multiple times, it will return the same path each time.
     *
     * @param string $versionsPath
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
}