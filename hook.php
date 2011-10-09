#!/usr/bin/env php
<?php

require __DIR__.'/autoload.php';

use Symfony\Component\Process\Process;
$redis = new Predis\Client();


// file_put_contents(__DIR__.'/hook.log', print_r($_SERVER, true), FILE_APPEND);

$strip_id = $_SERVER['STRIP_ID'];


switch ($_SERVER['ACTION']) {
    case 'init':
    case 'start':
    case 'stop':
        $redis->publish('booth:capture', json_encode(array(
            'action' => $_SERVER['ACTION'],
            'id'     => $strip_id
        )));

        break;
        
    case 'download':
        $file = realpath(getcwd().'/'.$_SERVER['ARGUMENT']);
        
        $redis->publish('booth:capture', json_encode(array(
            'action' => $_SERVER['ACTION'],
            'file'   => $file,
            'id'     => $strip_id
        )));
        
        $redis->rpush('photos:'.$strip_id, $file);
        break;
}

exit(0);