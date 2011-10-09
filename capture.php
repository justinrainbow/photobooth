#!/usr/bin/env php
<?php

require __DIR__.'/autoload.php';

use Symfony\Component\Process\Process;
$redis = new Predis\Client();

$strip_id = sha1(microtime(true));

$env = $_ENV;
$cmd = sprintf(
    'STRIP_ID=%s gphoto2 --capture-image-and-download --interval 1 --frames 3 --hook-script %s', 
    $strip_id, escapeshellarg( __DIR__.'/hook.php')
);
$dir = __DIR__.'/tools/raw/' . $strip_id;
mkdir($dir);

$process = new Process($cmd, $dir);
$process->run(function($type, $data) {
    echo $data;
});


