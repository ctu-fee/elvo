<?php

namespace Elvo\Mvc\ServiceManager;

use Zend\ServiceManager\Config;
use Zend\Mvc\Controller\ControllerManager;
use Elvo\Mvc\Controller\VoteController;


class ControllerConfig extends Config
{


    public function getInvokables()
    {
        return array(
            'Elvo\Controller\IndexController' => 'Elvo\Mvc\Controller\IndexController'
        );
    }


    public function getFactories()
    {
        return array(
            'Elvo\Controller\VoteController' => function (ControllerManager $cm)
            {
                $sm = $cm->getServiceLocator();
                $controller = new VoteController($sm->get('Elvo\AuthenticationService'), $sm->get('Elvo\CandidateService'));
                return $controller;
            }
        );
    }
}