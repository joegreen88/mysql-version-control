<?php
/**
 * Dynamic example config for use with adapter PhpFile.
 *
 * You can use raw php to implement your own logic and build the array.
 *
 * The code in this example is not a concrete implementation of any particular framework;
 * you can pick up the ideas and implement them in your own application or framework.
 *
 */

initialiseMyFrameworkOrEnvironment();

$conf = [
    'environments' => [
        'environments' => ['dev', 'production'],
        'testing_environments' => ['dev'],
    ]
];

// Idea: Only add the production database to the config if we are on the production environment
if ("production" === APP_ENV) {
    // You may have a set up where this file only exists in production
    $conf["production"] = require "prod-db-config.php";
}

// Idea: Use dev as a mask for multiple potential db configurations
if ("dev" === APP_ENV) {
    $conf["dev"] = getDbConfigForDeveloper(DEVELOPER_NAME);
}

return $conf;
