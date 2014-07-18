<?php
//http://sleep-er.co.uk/blog/2014/Creating-a-simple-REST-application-with-Silex-part3/
//http://techportal.inviqa.com/2014/01/29/manage-project-dependencies-with-bower-and-composer/
    
require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/config.php';


$app = new Silex\Application();
$app['root_dir'] = __DIR__;

require __DIR__ . '/src/Cnerta/app.php';

$app->run();