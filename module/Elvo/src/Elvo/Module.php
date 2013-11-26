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
        return new Mvc\ServiceManager\ServiceConfig();
    }


    public function getControllerConfig()
    {
        return new Mvc\ServiceManager\ControllerConfig();
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
            _dump('ERROR: ' . $e->getError());
            $exception = $e->getParam('exception');
            if ($exception) {
                _dump(sprintf("[%s] %s", get_class($exception), $exception->getMessage()));
            }
            
            // $e->setError(false);
        }, 1000);
    }
}