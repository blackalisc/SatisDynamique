<?php

namespace Cnerta\Services;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Cnerta\Services\Composer;
use Cnerta\Model\RepositoryFactory;

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
        
        $repository = array();
        
        foreach($this->satisConfig['repositories'] as $repo) {
            $repository[] = $repo;
        }
        
        
        return array("repositories" => $repository);
    }
    
    public function addPackage($name, $version)
    {   
        $this->cleanEntry($name);
        $this->cleanEntry($version);
        
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
        $this->cleanEntry($name);
        $this->prepareSatisConfig();
        
        if(!isset($this->satisConfig['require'][$name])) {
             throw new NotFoundHttpException(sprintf("This package dose not exist %s", $name));
        }
        
        unset($this->satisConfig['require'][$name]);
                
        $this->composer->writeSatisConf($this->satisConfig);
        
        return "ok";
    }
    
    public function addRepository($repository, $repositoryToUpdate = null)
    {
        $this->cleanEntry($repository);

        $this->prepareSatisConfig();

        $repoFactory = new RepositoryFactory($this->config);

        $repoFactory
                ->getManager($repository)
                ->addRepository($repository, $this->satisConfig['repositories'], $repositoryToUpdate);

        $this->composer->writeSatisConf($this->satisConfig);

        return "ok";
    }


    public function removeRepository($repository)
    {
        $this->prepareSatisConfig();
        
        $repoFactory = new RepositoryFactory($this->config);
        
        $repoFactory
                ->getManager($repository)
                ->deleteRepository($repository, $this->satisConfig['repositories']);

        $this->composer->writeSatisConf($this->satisConfig);
        
        return "ok";
    }

    private function prepareSatisConfig()
    {
        if($this->satisConfig == null) {
            $this->satisConfig = $this->composer->readComposerFile();
        }
    }
    
    

    private function cleanEntry(&$entry)
    {
        if (is_array($entry)) {           
            foreach ($entry as $key => $subEntry) {
                $entry[$key] = $this->cleanEntry($subEntry);
            }
            return $entry;
        }
        
        return $entry = trim($entry);        
    }
}
