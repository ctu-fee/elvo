<?php

namespace Elvo\Mvc\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


class IndexController extends AbstractActionController
{


    public function indexAction()
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