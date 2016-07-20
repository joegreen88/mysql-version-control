<?php
/**
 * Simple example config for use with adapter PhpFile.
 *
 * The structure matches that of db.ini.
 *
 * You can use raw php to implement your own logic and build the array.
 *
 */
return [
    'environments' => [
        'environments' => ['dev', 'production'],
        'testing_environments' => ['dev'],
    ],
    'dev' => [
        'buildtime' => [ // parameters can be hard coded...
            'host' => 'localhost',
            'port' => 3306,
            'user' => 'root',
            'password' => 'root-password',
            'database' => 'my_app'
        ],
        'runtime' => [ // parameters can be hard coded...
            'host' => 'localhost',
            'port' => 3306,
            'user' => 'developer',
            'password' => 'secret-password',
            'database' => 'my_app'
        ],
    ],
    'prod' => [
        'buildtime' => [ // ...or parameters can be sourced from elsewhere
            'host' => $_SERVER["PROD_MYSQL_HOST_BUILDTIME"],
            'port' => $_SERVER["PROD_MYSQL_PORT_BUILDTIME"],
            'user' => $_SERVER["PROD_MYSQL_USER_BUILDTIME"],
            'password' => $_SERVER["PROD_MYSQL_PASSWORD_BUILDTIME"],
            'database' => $_SERVER["PROD_MYSQL_DATABASE_BUILDTIME"]
        ],
        'runtime' => [ // ...or parameters can be sourced from elsewhere
            'host' => $_SERVER["PROD_MYSQL_HOST_RUNTIME"],
            'port' => $_SERVER["PROD_MYSQL_PORT_RUNTIME"],
            'user' => $_SERVER["PROD_MYSQL_USER_RUNTIME"],
            'password' => $_SERVER["PROD_MYSQL_PASSWORD_RUNTIME"],
            'database' => $_SERVER["PROD_MYSQL_DATABASE_RUNTIME"]
        ],
    ],
];
