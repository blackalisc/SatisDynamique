<?php

namespace Cnerta\Services;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

/**
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class Composer {

    protected $config;
    protected $satisConfig;

    public function __construct($config) {
        $this->config = $config;
        $this->config['composer_cache_path'] = $config['cache_path'] . "/composer/";
    }

    public function getComposer() {
        $process = new Process(sprintf("cd %s && curl -sS https://getcomposer.org/installer | php", $this->config['cache_path']));
        $process->run();
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

    public function validatePackage($satisConfig) {
        $this->mkdirComposerFolder();

        $composerJson = sprintf("%s/composer.json", $this->config['composer_cache_path']);

        file_put_contents($composerJson, json_encode($satisConfig));

        $process = new Process(sprintf("php %s/composer.phar validate", $this->config['composer_cache_path']));

        $process->run();
//
//        print_r($process->getOutput());
//        echo "\n";exit;
    }

    private function mkdirComposerFolder() {
        $fs = new Filesystem();
        if (!$fs->exists($this->config['composer_cache_path'])) {
            $fs->mkdir($this->config['composer_cache_path']);
        }
    }

}
