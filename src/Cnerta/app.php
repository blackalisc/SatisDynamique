<?php

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\RouteCollection;
use Silex\Application;

// Please set to false in a production environment
$app['debug'] = true;
$app['sd.conf'] = $config;

$app['sd.service.satis.manager'] = function($app) { return new \Cnerta\Services\SatisManager($app['sd.conf']); };

$app['routes'] = $app->extend(
        'routes',
        function (RouteCollection $routes, Application $app) {
            $loader = new YamlFileLoader(new FileLocator(__DIR__ . '/Resources/Config'));
            $collection = $loader->load('routes.yml');
            $routes->addCollection($collection);

            return $routes;
        }
);
