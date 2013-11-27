<?php

namespace Elvo\Mvc\Listener;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Log\Logger;
use Zend\Mvc\MvcEvent;
use Zend\Http\PhpEnvironment\Request;
use Zend\Mvc\Application;


class DispatchListener extends AbstractListenerAggregate
{

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var string
     */
    protected $uniqueId;


    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('dispatch', array(
            $this,
            'onDispatch'
        ));
        
        $this->listeners[] = $events->attach('dispatch.error', array(
            $this,
            'onPreDispatchError'
        ), 1000);
    }


    public function onDispatch(MvcEvent $event)
    {
        /* @var $application \Zend\Mvc\Application */
        $application = $event->getApplication();
        $sm = $application->getServiceManager();
        
        /* @var $request \Zend\Http\PhpEnvironment\Request */
        $request = $application->getRequest();
        /* @var $request \Zend\Http\PhpEnvironment\Response */
        $response = $application->getResponse();
        
        $uniqueId = $this->getUniqueId($application);
        _dump($this->formatLogDispatchMessage($uniqueId, $request, 'dispatch'));
    }


    public function onPreDispatchError(MvcEvent $event)
    {
        /* @var $application \Zend\Mvc\Application */
        $application = $event->getApplication();
        $sm = $application->getServiceManager();
        
        /* @var $request \Zend\Http\PhpEnvironment\Request */
        $request = $application->getRequest();
        
        $env = $sm->get('Elvo\Environment');
        $uniqueId = $this->getUniqueId($application);
        
        _dump($this->formatLogDispatchMessage($uniqueId, $request, sprintf("ERROR: %s", $event->getError())));
        $exception = $event->getParam('exception');
        if ($exception) {
            _dump($this->formatLogDispatchMessage($uniqueId, $request, sprintf("[%s] %s", get_class($exception), $exception->getMessage())));
            _dump("$exception");
        }
        
        if (! $env->isModeDevel()) {
            $event->stopPropagation(true);
            $event->setError(false);
            
            $response = $event->getResponse();
            $response->setStatusCode(400);
            return $response;
        }
    }


    protected function formatLogDispatchMessage($uniqueId, Request $request, $message)
    {
        return sprintf("[%s] %s %s: %s", $uniqueId, $request->getMethod(), $request->getUriString(), $message);
    }


    protected function getUniqueId(Application $application)
    {
        if (null === $this->uniqueId) {
            $sm = $application->getServiceManager();
            
            /* @var $authenticationService \Zend\Authentication\AuthenticationService */
            $authenticationService = $sm->get('Elvo\AuthenticationService');
            
            $this->uniqueId = 'anonymous';
            if ($authenticationService->hasIdentity()) {
                $this->uniqueId = substr(md5($authenticationService->getIdentity()->getId()), 0, 9);
            }
        }
        
        return $this->uniqueId;
    }
}