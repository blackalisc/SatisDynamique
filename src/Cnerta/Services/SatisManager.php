<?php

namespace Cnerta\Services;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class SatisManager
{
    protected $config;
    
    protected $satisConfig;
    
    public static $repositoryType = array('composer', 'vcs', 'svn', 'git', 'hg', 'pear', 'package');


    public function __construct($config)
    {
        $this->config = $config;
    }

    public function readComposerFile()
    {
        $fs = new Filesystem();
        if(!$fs->exists($this->config['satis_package_conf_path'])) {
            throw new \Exception("Composer json not found in " . $this->config['satis_package_conf_path']);
        }
        
        $rawSatisConfig = file_get_contents($this->config['satis_package_conf_path']);
        
        $this->satisConfig = json_decode($rawSatisConfig, true);            
    }
    
    public function getPackages()
    {
        $this->prepareSatisConfig();
        
        return $this->satisConfig['require'];
    }
    
    public function getRepositories()
    {
        $this->prepareSatisConfig();
        
        return $this->satisConfig['repositories'];
    }
    
    public function addPackage($name, $version)
    {      
        $this->prepareSatisConfig();
        
        if(isset($this->satisConfig['require'][$name])
           && $this->satisConfig['require'][$name] == $version
                ) {
             throw new \Exception(sprintf("This package already exist %s", $name));
        }
        
        $this->satisConfig['require'][$name] = $version;
        
        $this->writeSatisConf();
        
        return "ok";
    }
    
    public function removePackage($name)
    {      
        $this->prepareSatisConfig();
        
        if(!isset($this->satisConfig['require'][$name])) {
             throw new NotFoundHttpException(sprintf("This package dose not exist %s", $name));
        }
        
        unset($this->satisConfig['require'][$name]);
                
        $this->writeSatisConf();
        
        return "ok";
    }
    
    public function addRepository($type, $url)
    {
        
        if(!in_array($type, self::$repositoryType)) {
            throw new \BadMethodCallException(sprintf("The type :%s is unknow", $type));
        }
        
        if(!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \BadMethodCallException(sprintf("The URL :%s is not an URL", $url));
        }
        
        $this->prepareSatisConfig();
        
        foreach($this->satisConfig['repositories'] as $repo) {
            if($repo['type'] == $type && $repo['url'] == $url) {
                throw new \Exception(sprintf("This repository already exist %s", $url));
            }       
        }
        
        $this->satisConfig['repositories'][] = array(
            "type" => $type,
            "url" => $url
        );
        
        $this->writeSatisConf();
        
        return "ok";
    }
    
    public function removeRepository($type, $url)
    {
        
        if(!in_array($type, self::$repositoryType)) {
            throw new \BadMethodCallException(sprintf("The type: %s is unknow", $type));
        }
        
        if(!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \BadMethodCallException(sprintf("The URL: %s is not an URL", $url));
        }
        
        $this->prepareSatisConfig();
        
        foreach($this->satisConfig['repositories'] as $key => $repo) {
            if($repo['type'] == $type && $repo['url'] == $url) {
                
                unset($this->satisConfig['repositories'][$key]);
                
                $this->writeSatisConf();
                
                return "ok";
            }       
        }
        
        throw new NotFoundHttpException(sprintf("Type: %s and URL: %s not found", $type, $url));
    }
    
    
    
    private function writeSatisConf()
    {
        $satisConfPath = explode("/", $this->config['satis_package_conf_path']);
        $satisConfName = array_pop($satisConfPath);
        $satisConfPath = implode("/", $satisConfPath);
        
        $oldSatisConfPath = $satisConfPath . "/old_satis_conf/";
        
        $fs = new Filesystem();
        $fs->mkdir($oldSatisConfPath);
        
        $dt = new \DateTime();
        
        $fs->copy(
                $this->config['satis_package_conf_path'],
                sprintf("%s%s-%s", $oldSatisConfPath, $dt->format("Y-m-d H-i-s"), $satisConfName)
                );
        
        file_put_contents($this->config['satis_package_conf_path'], json_encode($this->satisConfig));
    }
    
    private function prepareSatisConfig()
    {
        if($this->satisConfig == null) {
            $this->readComposerFile();
        }
    }
}
