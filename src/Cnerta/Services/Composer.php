<?php

namespace Cnerta\Services;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File;
use Symfony\Component\Process\Process;

/**
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class Composer {

    protected $config;
    protected $satisConfig;

    public function __construct($config) {
        $this->config = $config;
        $this->config['composer_cache_path'] = $config['temp_path'] . "/composer";
    }

    public function getComposer($composer_path = null) {
        
        $composerFilePath = $composer_path ? $composer_path : $this->config['composer_cache_path'];
        
        $fs = new Filesystem();
        if(!$fs->exists($composerFilePath . "/composer.phar")) {
            $process = new Process(sprintf("cd %s && curl -sS https://getcomposer.org/installer | php", $composerFilePath));
            if(isset($this->config["process_env"])) {
                $process->setEnv($this->config["process_env"]);
            }
            $process->run();
            
            return;
        }
        
        $composerFile = new \SplFileInfo($composerFilePath . "/composer.phar");
        $date = new \DateTime();
        $date->setTimestamp($composerFile->getMTime());
        $date->add(new \DateInterval("P1D"));
        
        if($date < new \DateTime()) {
            $process = new Process(sprintf("cd %s &&  php composer.phar selfupdate", $composerFilePath));
            if(isset($this->config["process_env"])) {
                $process->setEnv($this->config["process_env"]);
            }
            $process->run();
        }
    }

    public function readComposerFile() {
        $fs = new Filesystem();
        if (!$fs->exists($this->config['satis_package_conf_path'])) {
            throw new \Exception("Composer json not found in " . $this->config['satis_package_conf_path']);
        }

        $rawSatisConfig = file_get_contents($this->config['satis_package_conf_path']);

        return $this->satisConfig = json_decode($rawSatisConfig, true);
    }

    public function writeSatisConf($satisConfig) {
                
        $satisConfPath = explode("/", $this->config['satis_package_conf_path']);
        $satisConfName = array_pop($satisConfPath);
        $satisConfPath = implode("/", $satisConfPath);

        $oldSatisConfPath = $satisConfPath . "/old_satis_conf/";

        $fs = new Filesystem();
        $fs->mkdir($oldSatisConfPath);

        $dt = new \DateTime();

        $fs->copy(
            $this->config['satis_package_conf_path'], sprintf("%s%s-%s", $oldSatisConfPath, $dt->format("Y-m-d H-i-s"), $satisConfName)
        );

        file_put_contents($this->config['satis_package_conf_path'], json_encode($satisConfig));
    }

    public function validatePackage($packageName, $version = null)
    {
        $this->mkdirComposerFolder();
                
        $process = new Process(sprintf("cd %s && php composer.phar show --name-only %s %s", $this->config['composer_cache_path'], $packageName, $version));
        
        $envArray = array("COMPOSER_HOME" => $this->config['composer_cache_path'] . "/.composer");
        if(isset($this->config["process_env"])) {
            $envArray = array_merge($envArray, $this->config["process_env"]);
        }
        $process->setEnv($envArray);
        $process->run();

        if($process->isSuccessful()) {
            return true;
        }
        
        throw new \ErrorException(sprintf("%s\nFail to write composer.json", $this->cleanProcesssOutput($process)));
    }

    private function mkdirComposerFolder() {
        $fs = new Filesystem();
        if (!$fs->exists($this->config['composer_cache_path'])) {
            $fs->mkdir($this->config['composer_cache_path']);
            $fs->mkdir($this->config['composer_cache_path'] . "/.composer");
            $this->getComposer();
        }
    }
    
    private function cleanProcesssOutput(Process $process)
    {
        $output = trim($process->getErrorOutput());
        
//        $output = preg_replace( '/\s+/', ' ', $output );
//        $output = preg_replace( "/\n+/", "n", $output );
        
        return $output;
    }
}
