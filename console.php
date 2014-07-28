#!/usr/bin/env php
<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = new Silex\Application();
$app['root_dir'] = __DIR__;

require_once __DIR__.'/config.php';
require __DIR__ . '/src/Cnerta/Assetic/Assetic.php';

$app['sd.conf'] = $config;

$console = require __DIR__ . '/src/Cnerta/console.php';
$console->run();