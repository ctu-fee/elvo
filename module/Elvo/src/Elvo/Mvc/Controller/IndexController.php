<?php

namespace Elvo\Mvc\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


class IndexController extends AbstractActionController
{


    public function indexAction()
    {
        $view = new ViewModel();
        
        return $view;
    }
}