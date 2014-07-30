<?php

////////////////////////////////////////////////////////////////////////////////
//////////////////////// Configuration ASSETIC /////////////////////////////////
////////////////////////////////////////////////////////////////////////////////


$app['assetic.enabled'] = true;
$app['assetic.path_to_cache'] = $config['cache_path'] ;
$app['assetic.path_to_web'] = $app['root_dir'] . '/web/asset';
$app['assetic.input.path_to_assets'] = $app['root_dir'] . '/src/Cnerta/Resources/assets';

$app['assetic.input.path_to_css'] = array(
    $app['assetic.input.path_to_assets'] . '/less/style.less',
    $app['root_dir'] . "/vendor/web/angular/angular-csp.css",
    $app['root_dir'] . "/vendor/web/angular-xeditable/dist/css/xeditable.css"
    );
$app['assetic.output.path_to_css'] = 'css/styles.css';

$app['assetic.input.path_to_js'] = array(
    $app['root_dir'] . '/vendor/web/jquery/dist/jquery.min.js',
    $app['root_dir'] . '/vendor/web/angular/angular.js',
    $app['root_dir'] . '/vendor/web/angular-resource/angular-resource.js',
    $app['root_dir'] . '/vendor/web/angular-sanitize/angular-sanitize.js',
    $app['root_dir'] . '/vendor/web/angular-xeditable/dist/js/xeditable.min.js',
    $app['root_dir'] . '/vendor/web/angular-bootstrap/ui-bootstrap.min.js',
    $app['root_dir'] . '/vendor/web/angular-bootstrap/ui-bootstrap-tpls.min.js',
    $app['root_dir'] . '/src/Cnerta/Resources/assets/js/main.js',
    $app['root_dir'] . '/src/Cnerta/Resources/assets/js/controller/*.js',
);
$app['assetic.output.path_to_js'] = 'js/scripts.js';


if (isset($app['assetic.enabled']) && $app['assetic.enabled']) {
    $app->register(new \SilexAssetic\AsseticServiceProvider(), array(
        'assetic.options' => array(
            'debug' => $app['debug'],
            'auto_dump_assets' => $app['debug'],
        )
    ));

    $app['assetic.filter_manager'] = $app->share(
        $app->extend('assetic.filter_manager', function ($fm, $app) use ($config) {
            
            $arrayReplacement = array("http://localhost/SatisDynamique" => $config['base_url']);
            
            $fm->set('replace_value', new Cnerta\Assetic\ReplaceValueFilter($arrayReplacement));
            
            $fm->set('lesscss', new \Assetic\Filter\LessFilter("/usr/bin/node", array("/usr/lib/node_modules")));
            $fm->set('yui_css', new \Assetic\Filter\Yui\CssCompressorFilter($app['root_dir'] . "/vendor/packagist/yuicompressor-bin/bin/yuicompressor.jar"));
            $fm->set('cssembed', new \Assetic\Filter\CssEmbedFilter($app['root_dir'] . "/vendor/bin/cssembed.jar"));
            
            $fm->set('yui_js', new \Assetic\Filter\Yui\JsCompressorFilter($app['root_dir'] . "/vendor/packagist/yuicompressor-bin/bin/yuicompressor.jar"));

            return $fm;
        })
    );
    
    $finder = new Symfony\Component\Finder\Finder();
    $finder->files()->in($app['assetic.path_to_cache']);
    
    if($finder->count() >= 10 ) {
        $fs = new \Symfony\Component\Filesystem\Filesystem;
        foreach ($finder as $file) {
            $fs->remove($file->getRealpath());
        }
    }

    $app['assetic.asset_manager'] = $app->share(
        $app->extend('assetic.asset_manager', function ($am, $app) {
            $am->set('styles', new Assetic\Asset\AssetCache(
                new Assetic\Asset\GlobAsset(
                    $app['assetic.input.path_to_css'],
                    array(
                        $app['assetic.filter_manager']->get('lesscss'),
                        $app['assetic.filter_manager']->get('yui_css'),
                        $app['assetic.filter_manager']->get('cssembed'),
                        )
                ),
                new \Assetic\Cache\FilesystemCache($app['assetic.path_to_cache'])
            ));
            $am->get('styles')->setTargetPath($app['assetic.output.path_to_css']);

            $am->set('scripts', new Assetic\Asset\AssetCache(
                new \Assetic\Asset\GlobAsset(
                        $app['assetic.input.path_to_js'],
                        array($app['assetic.filter_manager']->get('replace_value'))
                        /*,array($app['assetic.filter_manager']->get('yui_js'))*/
                        ),
                new \Assetic\Cache\FilesystemCache($app['assetic.path_to_cache'])
            ));
            $am->get('scripts')->setTargetPath($app['assetic.output.path_to_js']);

            return $am;
        })
    );

}
