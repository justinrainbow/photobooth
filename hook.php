#!/usr/bin/env php
<?php

file_put_contents(__DIR__.'/hook.log', print_r($_SERVER, true), FILE_APPEND);

switch ($_SERVER['ACTION']) {
    case 'init':
        break;
        
    case 'start':
        break;
        
    case 'download':
        $file = $_SERVER['ARGUMENT'];
        break;
        
    case 'stop':
        break;
}

exit(0);