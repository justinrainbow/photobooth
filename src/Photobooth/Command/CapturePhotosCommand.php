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
            ->addArgument('output', InputArgument::REQUIRED, 'Location to store captured images')
            ->addArgument('hook', InputArgument::REQUIRED, 'Hook script')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->killPTPProcesses();

        $finder = new ExecutableFinder();

        $gphoto2 = $finder->find('gphoto2');

        $cmd = sprintf(
            '%s --capture-image-and-download --interval 1 --frames 3 --hook-script %s', 
            $gphoto2,
            $input->getArgument('hook')
        );

        $process = new Process($cmd, $input->getArgument('output'));
        $process->run();

        if ($process->isSuccessful()) {
            $output->writeln('Photos taken.');
        } else {
            $output->writeln('Photos <warn>failed</warn>.');
        }
    }

    protected function killPTPProcesses()
    {
        $finder = new ExecutableFinder();

        $killall = $finder->find('killall');

        $cmd = sprintf('%s PTPCamera', $killall);

        $process = new Process($cmd);
        $process->run();
    }
}
