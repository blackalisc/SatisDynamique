#!/usr/bin/env php
<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = new Silex\Application();
$app['root_dir'] = __DIR__;

require __DIR__ . '/src/Cnerta/Assetic/Assetic.php';

$console = require __DIR__ . '/src/Cnerta/console.php';
$console->run();