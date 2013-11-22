<?php

namespace Elvo;

use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\ControllerProviderInterface;
use Zend\Mvc\ModuleRouteListener;


class Module implements ServiceProviderInterface, ControllerProviderInterface
{


    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/../../src/' . __NAMESPACE__
                )
            )
        );
    }


    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }


    public function getServiceConfig()
    {
        return new ServiceManager\ServiceConfig();
    }


    public function getControllerConfig()
    {
        return new ServiceManager\ControllerConfig();
    }


    public function onBootstrap(MvcEvent $event)
    {
        
        /* @var $events \Zend\EventManager\EventManager */
        $events = $event->getApplication()->getEventManager();
        
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($events);
        
        $events->attach('dispatch.error', function (MvcEvent $e)
        {
            // $e->stopPropagation(true);
            // _dump($e->getError());
        }, 1000);
    }
}