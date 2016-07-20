<?php

require_once realpath(dirname(__FILE__).'/../../../autoload.php');

$app = new \Symfony\Component\Console\Application('up');

foreach (\Smrtr\MysqlVersionControl\DbConfig::getEnvironments() as $env) {

    $app->add(
        new \Smrtr\MysqlVersionControl\Command\UpCommand($env)
    );
}

$app->run();
