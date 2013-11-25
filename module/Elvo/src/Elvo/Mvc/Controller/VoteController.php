<?php

namespace Elvo\Mvc\Controller;

use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;


class VoteController extends AbstractActionController
{


    public function roleAction()
    {
        $view = new ViewModel();
        
        $view->addChild($this->createNavbarViewModel(), 'mainNavbar');
        
        return $view;
    }


    public function formAction()
    {
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