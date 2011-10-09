<?php

namespace Photobooth\Command;


use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\ExecutableFinder;
use Photobooth\Process\CaptureCommand;

class CapturePhotosCommand extends BaseCommand
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

        $cmd = new CaptureCommand();
        $cmd->setHookScript($input->getArgument('hook'));

        $process = new Process($cmd->getCommand(), $input->getArgument('output'));
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
