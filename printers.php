#!/usr/bin/env php
<?php

exec("/usr/bin/lpstat -a", $printers);

$data = array();
foreach ($printers AS $printer) {
    if (preg_match('~^(?P<name>\S+)\s*(?P<msg>.+?)$~i', $printer, $match)) {
        $data[] = array(
            'name' => $match['name'],
            'msg'  => $match['msg']
        );
    }
}

echo json_encode($data);
