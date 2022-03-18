#!/usr/bin/env php
<?php
declare(strict_types=1);

$autoloadPath = '';
if (isset($GLOBALS['_composer_autoload_path'])) {
    $autoloadPath = $GLOBALS['_composer_autoload_path'];
} else {
    foreach (array(__DIR__ . '/../../autoload.php', __DIR__ . '/../vendor/autoload.php', __DIR__ . '/vendor/autoload.php') as $file) {
        if (file_exists($file)) {
            $autoloadPath = $file;
            break;
        }
    }
}

require_once $autoloadPath;

use Console\ReorganizeCommand;
use Console\SplitCommand;
use Symfony\Component\Console\Application;

$app = new Application('YamlSplitter', '1.0.0');
$app->add(new SplitCommand());
$app->add(new ReorganizeCommand());
$app->run();
