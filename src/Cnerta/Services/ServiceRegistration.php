<?php

$app['sd.service.composer'] = function($app) { return new \Cnerta\Services\Composer($app['sd.conf']); };
$app['sd.service.satis.manager'] = function($app) { return new \Cnerta\Services\SatisManager($app['sd.conf'], $app['sd.service.composer']); };
$app['sd.service.satis.updater'] = function($app) { return new \Cnerta\Services\SatisUpdater($app['sd.conf'], $app['sd.service.composer']); };