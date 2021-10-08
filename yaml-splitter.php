#!/usr/bin/env php
<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Console\ReorganizeCommand;
use Console\SplitCommand;
use Symfony\Component\Console\Application;

$app = new Application('YamlSplitter', '1.0.0');
$app->add(new SplitCommand());
$app->add(new ReorganizeCommand());
$app->run();
