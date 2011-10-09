<?php

namespace Photobooth\Process;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\ExecutableFinder;

class CaptureCommand
{
    private $interval = 1;
    private $frames   = 3;
    private $hook;
    
    public function __construct()
    {

    }
    
    public function setHookScript($script)
    {
        $this->hook = $script;
    }
    
    public function getCommand()
    {
        $options = array();
        
        foreach (array('interval', 'frames') AS $var) {
            $value = $this->{$var};
            
            if (!empty($value)) {
                $options[$var] = escapeshellarg($var);
            }
        }
        
        if (null !== $this->hook) {
            $options['hook-script'] = escapeshellarg($this->hook);
        }
        
        $args = array();
        foreach ($options AS $name => $value) {
            $args[] = sprintf('--%s %s', $name, $value);
        }
        
        $finder = new ExecutableFinder();
        $gphoto2 = $finder->find('gphoto2');
        
        $cmd = $gphoto2 . ' --capture-image-and-download ' . join(" ", $args);
        
        return $cmd;
    }
}