<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

$console = new Application('Satis Dynamique', '0.1');

require __DIR__ . "/Services/ServiceRegistration.php";

$app->boot();

$console
    ->register('statis:update')
    ->setDescription('Update Satis')
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
        
        $output->writeln("Start Satis update");
        
        $app['sd.service.satis.updater']->updateSatis($output);
        
        $output->writeln("End Satis update");
    })
;
$console
    ->register('statis:repositories:update')
    ->setDescription('Update Satis repositories')
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
        
        $output->writeln("Start Satis repositories update");
        
        $app['sd.service.satis.updater']->updateRepositoriesSatis($output);
        
        $output->writeln("End Satis repositories update");
    })
;

return $console;