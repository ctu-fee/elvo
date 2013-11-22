<?php

namespace Elvo\ServiceManager;

use Zend\ServiceManager\Config;


class ControllerConfig extends Config
{


    public function getInvokables()
    {
        return array(
            'Elvo\Controller\IndexController' => 'Elvo\Controller\IndexController'
        );
    }


    public function getFactories()
    {
        return array();
    }
}