<?php
/**
 * In this config file the database connection config is softcoded and sourced from the server environment.
 *
 * The buildtime and runtime connections are identical in this example.
 */

$dbConf = [
    'host' => $_SERVER["DB_HOST"],
    'user' => $_SERVER["DB_USERNAME"],
    'password' => $_SERVER["DB_PASSWORD"],
    'database' => $_SERVER["DB_NAME"],
    'port' => $_SERVER["DB_PORT"],
];

return [
    'environments' => [
        'environments' => ['development', 'qa', 'staging', 'production'],
        'testing_environments' => ['development', 'qa'],
    ],
    $_SERVER["APP_ENV"] => [
        'buildtime' => $dbConf,
        'runtime' => $dbConf,
    ]
];
