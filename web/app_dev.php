<?php
//http://sleep-er.co.uk/blog/2014/Creating-a-simple-REST-application-with-Silex-part3/
//http://techportal.inviqa.com/2014/01/29/manage-project-dependencies-with-bower-and-composer/

// This check prevents access to debug front controllers that are deployed by accident to production servers.
// Feel free to remove this, extend it, or make something more sophisticated.
if (isset($_SERVER['HTTP_CLIENT_IP'])
    || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    || !in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', 'fe80::1', '::1'))
) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}

require_once __DIR__ . '/../vendor/autoload.php';


$app = new Silex\Application();
$app['root_dir'] = __DIR__ . '/..';

require_once  __DIR__ . '/../config.php';

require __DIR__ . '/../src/Cnerta/app.php';

require __DIR__ . '/../src/Cnerta/Assetic/Assetic.php';

$dumper = $app['assetic.dumper'];
$dumper->dumpAssets();

$app->run();
