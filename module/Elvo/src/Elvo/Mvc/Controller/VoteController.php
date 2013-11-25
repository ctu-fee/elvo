<?php

namespace Elvo\Mvc\Controller;

use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\Authentication\AuthenticationService;


class VoteController extends AbstractActionController
{

    /**
     * @var AuthenticationService
     */
    protected $authAdapter;


    public function __construct(AuthenticationService $authService)
    {
        $this->setAuthService($authService);
    }


    /**
     * @return AuthenticationService
     */
    public function getAuthService()
    {
        return $this->authAdapter;
    }


    /**
     * @param AuthenticationService $authService
     */
    public function setAuthService(AuthenticationService $authService)
    {
        $this->authAdapter = $authService;
    }


    public function onDispatch(MvcEvent $event)
    {
        $authService = $this->getAuthService();
        $authService->authenticate();
        if (! $authService->hasIdentity()) {
            /* @var $response \Zend\Http\Response */
            $response = $this->getResponse();
            $response->setStatusCode(401);
            return $response;
        }
        
        return parent::onDispatch($event);
    }


    public function roleAction()
    {
        
        $view = new ViewModel();
        
        $view->addChild($this->createNavbarViewModel(), 'mainNavbar');
        
        return $view;
    }


    public function formAction()
    {
        //_dump($this->getAuthService()->getIdentity());
        
        $view = new ViewModel();
        $view->addChild($this->createNavbarViewModel(), 'mainNavbar');
        
        return $view;
    }


    public function confirmAction()
    {
        $view = new ViewModel();
        $view->addChild($this->createNavbarViewModel(), 'mainNavbar');
        
        return $view;
    }


    public function errorAction()
    {
        $view = new ViewModel(array(
            'heading' => 'Chyba',
            'infoText' => 'Detail chyby...'
        ));
        $view->addChild($this->createNavbarViewModel(), 'mainNavbar');
        
        return $view;
    }
    
    /*
     * 
     */
    protected function createNavbarViewModel()
    {
        $navbarView = new ViewModel();
        $navbarView->setTemplate('component/main-navbar');
        
        return $navbarView;
    }
}