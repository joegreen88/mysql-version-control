<?php

$app = new \Symfony\Component\Console\Application('up');

foreach (\Smrtr\MysqlVersionControl\DbConfig::getEnvironments() as $env) {

    $app->add(
        new \Smrtr\MysqlVersionControl\UpCommand($env)
    );
}

$app->run();
