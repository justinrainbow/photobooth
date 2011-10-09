<?php

require_once __DIR__.'/vendor/Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'Symfony'   => __DIR__.'/vendor',
    'Photobooth'=> __DIR__.'/src',
    'Predis'    => __DIR__.'/vendor/predis/lib',
));

$loader->register();