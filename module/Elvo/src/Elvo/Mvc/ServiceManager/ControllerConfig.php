<?php

namespace Elvo\Mvc\ServiceManager;

use Zend\ServiceManager\Config;
use Zend\Mvc\Controller\ControllerManager;
use Elvo\Mvc\Controller\VoteController;
use Elvo\Mvc\Controller\IndexController;


class ControllerConfig extends Config
{


    public function getFactories()
    {
        return array(
            'Elvo\Controller\IndexController' => function (ControllerManager $cm)
            {
                $sm = $cm->getServiceLocator();
                $controller = new IndexController($sm->get('Elvo\AuthenticationService'));
                return $controller;
            },
            
            'Elvo\Controller\VoteController' => function (ControllerManager $cm)
            {
                $sm = $cm->getServiceLocator();
                $controller = new VoteController($sm->get('Elvo\AuthenticationService'), $sm->get('Elvo\CandidateService'), $sm->get('Elvo\Translator'));
                return $controller;
            }
        );
    }
}