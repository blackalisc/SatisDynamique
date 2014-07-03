<?php

// Please set to false in a production environment
$app['debug'] = true;
$app['sd.conf'] = $config;

$app['sd.service.satis.manager'] = function($app) { return new \Cnerta\Services\SatisManager($app['sd.conf']); };


$app->mount('/', new Cnerta\Controller\SatisController());