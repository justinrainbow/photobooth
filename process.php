<?php

$rawImageDir     = __DIR__ . '/raw';
$processImageDir = __DIR__ . '/processed';
$toPrintDir      = __DIR__ . '/toprint';

$rawImages = glob($rawImageDir . '/*.*');
$totalImages = count($rawImages);


if ($totalImages == 4) {
    $images = new Imagick($rawImages);
    
    printf("Height: %s, Width: %s\n", $images->getImageHeight(), $images->getImageWidth());

    foreach ($images AS $image) {
        $image->thumbnailImage(600, null);
        
        // add some contrast
        //$image->contrastImage(1);
        
        // add some adaptive blur
        $image->adaptiveBlurImage(1, 1);
        
        $image->borderImage(new ImagickPixel('#ffffff'), 12, 12);
        
        $image->fxImage('(1.0/(1.0+exp(10.0*(0.5-u)))-0.006693)*1.0092503');
    }

    $images->resetIterator();
    $canvas = $images->appendImages(true);

    $canvas->setImageFormat("jpg");
    
    $canvas->writeImage($toPrintDir . '/strip.jpg');
}