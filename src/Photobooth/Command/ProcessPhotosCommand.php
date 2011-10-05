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

use Photobooth\PhotoStrip;

class ProcessPhotosCommand extends Command
{
    public function configure()
    {
        $this
            ->setName('photos:process')
            ->addArgument('source', InputArgument::REQUIRED, 'Directory with original photos')
            ->addOption('output', 'o', InputOption::VALUE_REQUIRED, 'Destination filename for photo strip')
            ->addOption('limit', 'l', InputOption::VALUE_OPTIONAL, 'Number of photos to use in the strip.', 3)
        ;
    }
    
    protected function findImages($source, $limit = 3)
    {
        $images = glob($source . '/*.*');

        if (count($images) > $limit) {
            return array_slice($images, -($limit));
        }
        
        if (empty($images) || count($images) !== $limit) {
            return false;
        }
        
        return $images;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $rawImageDir     = $input->getArgument('source');
        $destination     = $input->getOption('output');
        
        $dpi = 300;
        $width = 4;
        $height = 6;
        
        $dimensions = array(
            'width'  => $dpi * $width,  // 4 inches * 72 dpi = 288px
            'height' => $dpi * $height, // 6 inches * 72 dpi = 432px
        );
        
        $rawImages = $this->findImages($rawImageDir, $input->getOption('limit'));

        if (false === $rawImages) {
            $output->writeln('Nothing to do.');
            return;
        }
        
        $photoStrip = new PhotoStrip($dimensions['width'], $dimensions['height']);
        $photoStrip->setBackgroundColor(new \ImagickPixel('white'));

        foreach ($rawImages AS $file) {
            $image = new \Imagick($file);
            
            $image->rotateImage(new \ImagickPixel(), -90);

            $photoStrip->addImage($image);
        }

        $canvas = $photoStrip->generate();
        $canvas->setImageFormat("jpg");

        $canvas->writeImage($destination);
        
        $output->writeln(sprintf('Saved photo strip to <info>%s</info>', $destination));
    }
}
