<?php

namespace Elvo\Mvc\ServiceManager;

use Zend\ServiceManager\Config;


class ControllerConfig extends Config
{


    public function getInvokables()
    {
        return array(
            'Elvo\Controller\IndexController' => 'Elvo\Mvc\Controller\IndexController',
            'Elvo\Controller\VoteController' => 'Elvo\Mvc\Controller\VoteController'
        );
    }


    public function getFactories()
    {
        return array();
    }
}