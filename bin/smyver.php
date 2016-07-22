#!/usr/bin/env php
<?php
/**
 * @package Smrtr\MysqlVersionControl\Bin
 * @author Joe Green <joe.green@smrtr.co.uk>
 */

require_once realpath(dirname(__FILE__).'/../../../autoload.php');

$app = new \Symfony\Component\Console\Application('smyver');

$app->add(new \Smrtr\MysqlVersionControl\Command\Status);
$app->add(new \Smrtr\MysqlVersionControl\Command\Up);
$app->add(new \Smrtr\MysqlVersionControl\Command\Teardown);

$app->run();
