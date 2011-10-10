#!/usr/bin/env php
<?php

require __DIR__.'/autoload.php';

use Symfony\Component\Process\Process;
use Symfony\Component\Process\ExecutableFinder;

$cfg = json_decode(file_get_contents(__DIR__.'/config.json'), true);

$redis = new Predis\Client();

function killall($name) {
    $finder = new ExecutableFinder();

    $killall = $finder->find('killall');

    $cmd = sprintf('%s %s', $killall, $name);

    $process = new Process($cmd);
    $process->run();
}

// The PTPCamera process gets in the way of gphoto2... kill it!
killall('PTPCamera');

$strip_id = sha1(microtime(true));

$env = $_ENV;
$cmd = sprintf(
    'STRIP_ID=%s gphoto2 --capture-image-and-download --interval %s --frames 3 --hook-script %s', 
    $strip_id, $cfg['waittime'], escapeshellarg( __DIR__.'/hook.php')
);
$dir = __DIR__.'/files/photos/' . $strip_id;
mkdir($dir);

$process = new Process($cmd, $dir);
$process->run(function($type, $data) {
    // echo $data;
});

if ($process->isSuccessful()) {
    echo $strip_id;
} else {
    exit(1);
}


