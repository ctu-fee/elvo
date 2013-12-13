<?php

namespace Elvo\Mvc\Listener;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventInterface;
use Zend\Mvc\MvcEvent;
use Monolog\Logger;
use Psr\Log\LoggerInterface;


class ApplicationEventsListener extends AbstractListenerAggregate
{

    /**
     * @var LoggerInterface
     */
    protected $logger;


    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }


    /**
     * @param LoggerInterface $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }


    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('elvo.log', array(
            $this,
            'onLog'
        ));
        
        $this->listeners[] = $events->attach('dispatch', array(
            $this,
            'onLogDispatch'
        ), 10);
        
        /*
        $this->listeners[] = $events->attach('dispatch.error', array(
            $this,
            'onLogDispatchError'
        ), 1001);
        */
    }
    
    /*
    public function onLogDispatchError(MvcEvent $event)
    {
        $this->log(sprintf("ERROR: %s", $event->getError()), Logger::ERROR);
        $exception = $event->getParam('exception');
        if ($exception) {
            $this->log(sprintf("EXCEPTION [%s] %s\n%s", get_class($exception), $exception->getMessage(), $exception->getTraceAsString()), Logger::ERROR);
        }
    }
    */
    public function onLogDispatch(MvcEvent $event)
    {
        $this->log('DISPATCH');
    }


    public function onLog(EventInterface $event)
    {
        $this->log($event->getParam('message'), $event->getParam('level'));
    }


    protected function log($message, $level = Logger::INFO)
    {
        $this->getLogger()->log($level, sprintf("%s %s: %s", $_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'], $message));
    }
}