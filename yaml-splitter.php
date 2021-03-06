#!/usr/bin/env php
<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Console\SplitCommand;
use Symfony\Component\Console\Application;

$app = new Application();
$app->add(new SplitCommand());
$app->run();
