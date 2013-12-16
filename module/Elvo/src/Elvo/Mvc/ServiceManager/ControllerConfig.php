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
                
                $controller = new IndexController($sm->get('Elvo\AuthenticationService'), $sm->get('Elvo\Domain\VoteManager'));
                $controller->setEventManager($sm->get('Elvo\EventManager'));
                
                return $controller;
            },
            
            'Elvo\Controller\VoteController' => function (ControllerManager $cm)
            {
                $sm = $cm->getServiceLocator();
                
                $authService = $sm->get('Elvo\AuthenticationService');
                $voteManager = $sm->get('Elvo\Domain\VoteManager');
                $voteService = $sm->get('Elvo\Domain\VoteService');
                $candidateService = $sm->get('Elvo\CandidateService');
                $translator = $sm->get('Elvo\Translator');
                
                $controller = new VoteController($authService, $voteManager, $voteService, $candidateService, $translator);
                $controller->setEventManager($sm->get('Elvo\EventManager'));
                
                return $controller;
            }
        );
    }
}