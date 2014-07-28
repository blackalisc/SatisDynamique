<?php

namespace Cnerta\Services;

use Symfony\Component\Process\Process;
use Symfony\Component\Console\Output\OutputInterface;
use Cnerta\Services\Composer;

/**
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class SatisUpdater
{
   /**
     * @var \Cnerta\Services\Composer 
     */
    protected $composer;
    
    protected $config;

    public function __construct($config, Composer $composer)
    {
        $this->config = $config;
        $this->composer = $composer;
    }
 
    /**
     * Update the Satis program
     * 
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return boolean
     * @throws \ErrorException
     */
    public function updateSatis(OutputInterface $output)
    {        
        $this->composer->getComposer($this->config['satis_base_path']);

        $process = new Process(sprintf("cd %s && git pull && php composer.phar install", $this->config['satis_base_path']));
        if (isset($this->config["process_env"])) {
            $process->setEnv($this->config["process_env"]);
        }
        
        $process->run(function ($type, $buffer) use ($output) {
            if (Process::ERR !== $type) {
                $output->write($buffer);
            }
        });

        if ($process->isSuccessful()) {
            return true;
        }
        
        throw new \ErrorException(sprintf("%s\n%s\nFail to update Satis", $process->getErrorOutput(), $process->getOutput()));
    }
 
    /**
     * Call Satis for update repositories
     * 
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return boolean
     * @throws \ErrorException
     */
    public function updateRepositoriesSatis(OutputInterface $output)
    {        
        $this->composer->getComposer($this->config['satis_base_path']);

        $process = new Process(
                sprintf("cd %s &&  php bin/satis build %s %s",
                    $this->config['satis_base_path'],
                    $this->config['satis_package_conf_path'],
                    $this->config['satis_build_path']));
        
        $process->setIdleTimeout(null);
        $process->setTimeout(null);
        
        if (isset($this->config["process_env"])) {
            $process->setEnv($this->config["process_env"]);
        }
        
        $process->run(function ($type, $buffer) use ($output) {
            if (Process::ERR !== $type) {
                $output->write($buffer);
            }
        });

        if ($process->isSuccessful()) {
            return true;
        }
        
        throw new \ErrorException(sprintf("%s\n%s\nFail to update Satis", $process->getErrorOutput(), $process->getOutput()));
    }

}
