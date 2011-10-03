<?php

namespace Photobooth;

use Imagick;
use ImagickPixel;

class PhotoStrip
{
    private $height;
    private $width;
    private $images = array();

    public function __construct($width = null, $height = null)
    {
        $this->setWidth($width);
        $this->setHeight($height);
    }
    
    public function setWidth($width)
    {
        $this->width = $width;
    }
    
    public function getWidth()
    {
        return $this->width;
    }
    
    public function setHeight($height)
    {
        $this->height = $height;
    }
    
    public function getHeight()
    {
        return $this->height;
    }
    
    public function addImage(Imagick $image)
    {
        $this->images[] = $image;
    }
    
    public function getImages()
    {
        return $this->images;
    }
    
    public function generate()
    {
        $photoStrip = new \Imagick();
        $photoStrip->newImage($this->getWidth(), $this->getHeight(), new ImagickPixel('white'));
        
        $x = 0;
        $y = 0;
        
        $width = $this->getWidth() / 2;
        $height = $this->getHeight() / count($this->images);
        
        foreach ($this->images AS $image) {
            $image = $this->prepareImage($image, $width, $height);
            
            $photoStrip->compositeImage($image, \Imagick::COMPOSITE_DEFAULT, $x, $y);
            
            $y += $image->getImageHeight();
        }
        
        return $photoStrip;
    }
    
    protected function prepareImage(Imagick $image, $width, $height)
    {
        $image->setGravity(Imagick::GRAVITY_CENTER);
        $image->resizeImage($width, null, Imagick::FILTER_LANCZOS, 1, false);
        
        if ($image->getImageHeight() > $height) {
            $y = ($image->getImageHeight() - $height) / 2;
            
            $image->cropImage($width, $height, 0, $y);
        }

        // add some contrast
        //$image->contrastImage(1);

        // add some adaptive blur
        $image->adaptiveBlurImage(1, 1);

        // $image->borderImage(new \ImagickPixel('#ffffff'), 12, 6);

        // make it pop!
        $image->fxImage('(1.0/(1.0+exp(10.0*(0.5-u)))-0.006693)*1.0092503');
        
        return $image;
    }
}
