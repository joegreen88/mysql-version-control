#!/usr/bin/env php
<?php
/**
 * @package Smrtr\MysqlVersionControl\Bin
 * @author Joe Green <joe.green@smrtr.co.uk>
 */

$app = new \Symfony\Component\Console\Application('smyver');

$app->add(new \Smrtr\MysqlVersionControl\Command\Up);

$app->run();
