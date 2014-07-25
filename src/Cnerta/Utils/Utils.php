<?php

namespace Cnerta\Utils;

/**
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class Utils
{

    public static function setUpPorxyConfig(&$config)
    {
        if ($config['server_proxy_ip'] != "") {
            $config['process_env'] = array(
                "HTTP_PROXY" => $config['server_proxy_ip'],
                "HTTPS_PROXY" => $config['server_proxy_ip'],
                "http_proxy" => $config['server_proxy_ip'],
                "https_proxy" => $config['server_proxy_ip'],
                "HTTP_PROXY_REQUEST_FULLURI" => $config['server_proxy_request_fulluri'] ? $config['server_proxy_request_fulluri'] : true,
                "HTTPS_PROXY_REQUEST_FULLURI" => $config['server_proxy_request_fulluri'] ? $config['server_proxy_request_fulluri'] : true,
                "http_proxy_request_fulluri" => $config['server_proxy_request_fulluri'] ? $config['server_proxy_request_fulluri'] : true,
                "https_proxy_request_fulluri" => $config['server_proxy_request_fulluri'] ? $config['server_proxy_request_fulluri'] : true);
        }
    }
}
