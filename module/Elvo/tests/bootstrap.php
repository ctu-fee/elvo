<?php
require __DIR__ . '/../../../vendor/autoload.php';

use Zend\Loader\AutoloaderFactory;

define('ELVO_TESTS_DIR', __DIR__);
define('ELVO_TESTS_DATA_DIR', ELVO_TESTS_DIR . '/data');
define('ELVO_DB_SCRIPTS_DIR', __DIR__ . '/../db');

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