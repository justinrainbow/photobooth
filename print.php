#!/usr/bin/env php
<?php

$cfg = json_decode(file_get_contents(__DIR__.'/config.json'), true);

$file = __DIR__.'/files/strips/'.$argv[1].'.jpg';

if (file_exists($file)) {
  exec(sprintf('/usr/bin/lp -d "%s" -o page-top=0 -o page-bottom=0 -o scaling=100 %s', $cfg['printer'], escapeshellarg($file)), $output);
  file_put_contents(__DIR__.'/printer.log', json_encode($output)."\n", FILE_APPEND);
}
