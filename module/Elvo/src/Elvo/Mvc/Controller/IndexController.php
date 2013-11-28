<?php

namespace Elvo\Mvc\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;
use Elvo\Domain\Vote\VoteManager;


class IndexController extends AbstractActionController
{

    /**
     * @var AuthenticationService
     */
    protected $authService;

    /**
     * 
     * @var VoteManager
     */
    protected $voteManager;

    protected $timeFormat = 'd.n.Y H:i';


    /**
     * Constructor.
     * 
     * @param AuthenticationService $authService
     * @param VoteManager $voteManager
     */
    public function __construct(AuthenticationService $authService, VoteManager $voteManager)
    {
        $this->setAuthService($authService);
        $this->setVoteManager($voteManager);
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


    /**
     * @return VoteManager
     */
    public function getVoteManager()
    {
        return $this->voteManager;
    }


    /**
     * @param VoteManager $voteManager
     */
    public function setVoteManager(VoteManager $voteManager)
    {
        $this->voteManager = $voteManager;
    }


    public function indexAction()
    {
        $this->getAuthService()->clearIdentity();
        /*
         * Main view
         */
        $view = new ViewModel(array(
            'startTime' => $this->getVoteManager()
                ->getStartTime()
                ->format($this->timeFormat),
            'endTime' => $this->getVoteManager()
                ->getEndTime()
                ->format($this->timeFormat)
        ));
        
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