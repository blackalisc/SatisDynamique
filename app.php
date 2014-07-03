<?php
require_once __DIR__.'/vendor/autoload.php';

$config = array(
    "satis_package_conf_path" => __DIR__ . "/composer_satis.json",
    "satis_bin_path" => "",
    "satis_html_path" => "",
    "composer_phar_path" => __DIR__ . "/../Composer/composer.phar",
);

//http://sleep-er.co.uk/blog/2014/Creating-a-simple-REST-application-with-Silex-part3/

//$filename = __DIR__.preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
//if (php_sapi_name() === 'cli-server' && is_file($filename)) {
//    return false;
//}

$app = new Silex\Application();

require __DIR__ . '/src/Cnerta/app.php';

$app->run();