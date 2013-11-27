#!/usr/bin/env php
<?php
use Symfony\Component\Console\Application;

require __DIR__ . '/../vendor/autoload.php';

$application = new Application();
$application->run();

// ------------
function _dump($value)
{
    error_log(print_r($value, true));
}