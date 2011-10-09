#!/usr/bin/env php
<?php

$cfg = json_decode(file_get_contents(__DIR__.'/config.json'), true);

$file = $argv[1];

if (file_exists($file)) {
    exec(sprintf('/usr/bin/lp -d "%s" -o page-top=0 -o page-bottom=0 -o scaling=100 %s', $cfg['printer'], escapeshellarg($file)));
}