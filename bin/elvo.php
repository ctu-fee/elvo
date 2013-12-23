#!/usr/bin/env php
<?php
use Zend\ServiceManager\ServiceManager;
use Elvo\Console;
use Elvo\Mvc;

require __DIR__ . '/../vendor/autoload.php';

$serviceManager = new ServiceManager();

$serviceConfig = new Mvc\ServiceManager\ServiceConfig();
$serviceConfig->configureServiceManager($serviceManager);

$consoleServiceConfig = new Console\ServiceManager\ServiceConfig();
$consoleServiceConfig->configureServiceManager($serviceManager);

$serviceManager->setService('Config', require __DIR__ . '/../config/autoload/elvo.local.php');

$application = $serviceManager->get('Elvo\Console\Application');
$application->run();

// ------------
function _dump($value)
{
    error_log(print_r($value, true));
}