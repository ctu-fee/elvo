#!/usr/bin/env php
<?php

use Zend\ServiceManager\ServiceManager;
use Symfony\Component\Console\Application;
use Elvo\Console;
use Elvo\Mvc;

require __DIR__ . '/../vendor/autoload.php';

$serviceManager = new ServiceManager();

$serviceConfig = new Mvc\ServiceManager\ServiceConfig();
$serviceConfig->configureServiceManager($serviceManager);

$consoleServiceConfig = new Console\ServiceManager\ServiceConfig();
$consoleServiceConfig->configureServiceManager($serviceManager);

$serviceManager->setService('Config', require __DIR__ . '/../config/autoload/elvo.local.php');

$application = new Application();
$application->addCommands(array(
    $serviceManager->get('Elvo\Console\VoteCountCommand')
));
$application->run();

// ------------
function _dump($value)
{
    error_log(print_r($value, true));
}