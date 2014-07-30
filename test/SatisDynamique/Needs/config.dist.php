<?php

$config = array(
    "base_path" => __DIR__ . "/../../..",
    "base_url" => "http://localhost",
    "satis_package_conf_path" => __DIR__ . "/../tmp/composer_satis.json",
    "cache_path" => __DIR__ . "/../tmp/cache",
    "temp_path" => __DIR__ . "/../temp",
    "satis_base_path" => "",
    "satis_build_path" => "",
    "satis_html_path" => "",
    "server_proxy_ip" => null,
    "server_proxy_request_fulluri" => null
);

if(is_file(__DIR__ . "/config.php")) {
    require __DIR__ . "/config.php";
}