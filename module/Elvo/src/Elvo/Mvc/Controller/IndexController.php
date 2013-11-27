<?php

namespace Elvo\Mvc\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;


class IndexController extends AbstractActionController
{

    /**
     * @var AuthenticationService
     */
    protected $authService;


    public function __construct(AuthenticationService $authService)
    {
        $this->setAuthService($authService);
    }


    /**
     * @return AuthenticationService
     */
    public function getAuthService()
    {
        return $this->authService;
    }


    /**
     * @param AuthenticationService $authService
     */
    public function setAuthService(AuthenticationService $authService)
    {
        $this->authService = $authService;
    }


    public function indexAction()
    {
        $this->getAuthService()->clearIdentity();
        /*
         * Main view
         */
        $view = new ViewModel();
        
        /*
         * Navbar view
         */
        $navbarView = new ViewModel();
        $navbarView->setTemplate('component/main-navbar');
        $view->addChild($navbarView, 'mainNavbar');
        
        return $view;
    }


    public function autherrorAction()
    {
        /*
         * Main view
        */
        $view = new ViewModel();
        
        /*
         * Navbar view
        */
        $navbarView = new ViewModel();
        $navbarView->setTemplate('component/main-navbar');
        $view->addChild($navbarView, 'mainNavbar');
        
        return $view;
    }
}