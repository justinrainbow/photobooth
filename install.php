#!/usr/bin/env php
<?php

require __DIR__.'/autoload.php';

use Symfony\Component\Process\ExecutableFinder;

$name     = 'com.symplelabs.photobooth.plist';
$template = file_get_contents(__DIR__.'/etc/'.$name);
$installTo= $argv[1];

$finder = new ExecutableFinder();

file_put_contents(
    $installTo,
    strtr($template, array(
        '{{ path }}' => __DIR__,
        '{{ user }}' => $_SERVER['USER'],
        '{{ bin.node }}' => $finder->find('node')
    ))
);
