<?php

namespace Photobooth;

use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
    private $rootDir;
    private $config;
    
    public function setRootDir($dir)
    {
        $this->rootDir = $dir;
    }
    
    public function getRootDir()
    {
        return $this->rootDir;
    }
    
    public function getConfig()
    {
        if (null === $this->config) {
            $this->config = json_decode(file_get_contents($this->getRootDir().'/config.json'), true);
        }
        
        return $this->config;
    }
}