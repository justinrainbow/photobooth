<?php

namespace Photobooth\Command;

use Symfony\Component\Console\Command\Command;

use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\ExecutableFinder;

class CapturePhotosCommand extends Command
{
    public function configure()
    {
        $this
            ->setName('photos:capture')
            // ->addArgument('old', InputArgument::REQUIRED, 'Working copy to diff')
            // ->addArgument('new', InputArgument::REQUIRED, 'Working copy to diff')
            ->addOption('script', 's', InputOption::VALUE_OPTIONAL, 'The AppleScript file to run.', 'tools/capture.scpt')
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $finder = new ExecutableFinder();
        
        $osascript = $finder->find('osascript');
        $script    = stream_resolve_include_path($input->getOption('script'));
        
        $cmd = sprintf('%s %s', $osascript, escapeshellarg($script));
        
        $process = new Process($cmd);
        $process->run();
        
        if ($process->isSuccessful()) {
            $output->writeln('Photos taken.');
        } else {
            $output->writeln('Photos <warn>failed</warn>.');
        }
    }
}
