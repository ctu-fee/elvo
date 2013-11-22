<?php
require __DIR__ . '/../../../vendor/autoload.php';

use Zend\Loader\AutoloaderFactory;

AutoloaderFactory::factory(array(
    'Zend\Loader\StandardAutoloader' => array(
        'namespaces' => array(
            'Elvo' => __DIR__ . '/../src/Elvo'
        )
    )
));

// ----------
function _dump($value)
{
    error_log(print_r($value, true));
}