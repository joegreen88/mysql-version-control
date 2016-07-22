<?php
/**
 * In this config file we have two database connections sourced from the server environment.
 *
 * Both databases have identical buildtime and runtime connections in this example.
 */

$dbConfEmployees = [
    'host' => $_SERVER["DB_EMPLOYEES_HOST"],
    'user' => $_SERVER["DB_EMPLOYEES_USERNAME"],
    'password' => $_SERVER["DB_EMPLOYEES_PASSWORD"],
    'database' => $_SERVER["DB_EMPLOYEES_NAME"],
    'port' => $_SERVER["DB_EMPLOYEES_PORT"],
];

$dbConfWeb = [
    'host' => $_SERVER["DB_WEB_HOST"],
    'user' => $_SERVER["DB_WEB_USERNAME"],
    'password' => $_SERVER["DB_WEB_PASSWORD"],
    'database' => $_SERVER["DB_WEB_NAME"],
    'port' => $_SERVER["DB_WEB_PORT"],
];

return [
    'environments' => [
        'environments' => [
            'employees-development', 'employees-qa', 'employees-staging', 'employees-production',
            'web-development', 'web-qa', 'web-staging', 'web-production',
        ],
        'testing_environments' => [
            'employees-development', 'employees-qa',
            'web-development', 'web-qa',
        ],
    ],
    "employees-".$_SERVER["APP_ENV"] => [
        'buildtime' => $dbConfEmployees,
        'runtime' => $dbConfEmployees,
    ],
    "web-".$_SERVER["APP_ENV"] => [
        'buildtime' => $dbConfWeb,
        'runtime' => $dbConfWeb,
    ]
];
