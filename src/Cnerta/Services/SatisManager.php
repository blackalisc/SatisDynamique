<?php

namespace Cnerta\Services;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Cnerta\Services\Composer;

/**
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class SatisManager
{
    /**
     * @var \Cnerta\Services\Composer 
     */
    protected $composer;
    
    protected $config;
    
    protected $satisConfig;
    
    public static $repositoryType = array('composer', 'vcs', 'svn', 'git', 'hg', 'pear', 'package');


    public function __construct($config, Composer $composer)
    {
        $this->config = $config;
        $this->composer = $composer;
    }
    
    public function getPackages()
    {
        $this->prepareSatisConfig();
        
        $packages = array();
        
        foreach($this->satisConfig['require'] as $name => $version) {
            $packages[] = array("name" => $name, "version" => $version);
        }
        
        return array("packages" => $packages);
    }
    
    public function getRepositories()
    {
        $this->prepareSatisConfig();
        
        return array("repositories" => $this->satisConfig['repositories']);
    }
    
    public function addPackage($name, $version)
    {      
        $this->prepareSatisConfig();
        
        if(isset($this->satisConfig['require'][$name])
           && $this->satisConfig['require'][$name] == $version
                ) {
             throw new \Exception(sprintf("This package already exist %s", $name));
        }
        
        if($this->composer->validatePackage($name, $version)) {
        
            $this->satisConfig['require'][$name] = $version;

            $this->composer->writeSatisConf($this->satisConfig);

            return "ok";
        }
        
        return "ko";
    }
    
    public function removePackage($name)
    {      
        $this->prepareSatisConfig();
        
        if(!isset($this->satisConfig['require'][$name])) {
             throw new NotFoundHttpException(sprintf("This package dose not exist %s", $name));
        }
        
        unset($this->satisConfig['require'][$name]);
                
        $this->composer->writeSatisConf($this->satisConfig);
        
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
        
        $this->composer->writeSatisConf($this->satisConfig);
        
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
                
                $this->composer->writeSatisConf($this->satisConfig);
                
                return "ok";
            }       
        }
        
        throw new NotFoundHttpException(sprintf("Type: %s and URL: %s not found", $type, $url));
    }
    
    
    private function prepareSatisConfig()
    {
        if($this->satisConfig == null) {
            $this->satisConfig = $this->composer->readComposerFile();
        }
    }
}
