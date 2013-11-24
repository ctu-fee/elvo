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
        $view = new ViewModel(
            array(
                'heading' => 'Vitejte u voleb',
                'buttonVoteText' => 'Prejit k volbam'
            ));
        
        /*
         * Navbar view
         */
        $navbarView = new ViewModel(array(
            'title' => 'Volby'
        ));
        $navbarView->setTemplate('component/main-navbar');
        $view->addChild($navbarView, 'mainNavbar');
        
        return $view;
    }
}