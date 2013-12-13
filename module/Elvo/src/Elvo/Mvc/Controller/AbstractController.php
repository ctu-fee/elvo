<?php

namespace Elvo\Mvc\Controller;

use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Authentication\AuthenticationService;
use Elvo\Domain\Vote\VoteManager;


class AbstractController extends AbstractActionController
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


    /**
     * Returns the current user's identity.
     *
     * @return Identity
     */
    protected function getIdentity()
    {
        return $this->getAuthService()->getIdentity();
    }


    /**
     * Initializes the general action view.
     *
     * @param array $params
     * @return ViewModel
     */
    protected function initView(array $params = array())
    {
        $view = new ViewModel($params);
        $view->addChild($this->createNavbarViewModel(), 'mainNavbar');
        return $view;
    }


    /**
     * Returns the main navigation bar's view model.
     * @return ViewModel
     */
    protected function createNavbarViewModel()
    {
        $navbarView = new ViewModel(array(
            'contactEmail' => $this->getVoteManager()->getContactEmail()
        ));
        $navbarView->setTemplate('component/main-navbar');
        
        return $navbarView;
    }


    /**
     * Logs the exception.
     * 
     * @param \Exception $e
     */
    protected function logException(\Exception $e)
    {
        $message = sprintf("[%s] %s\n%s", get_class($e), $e->getMessage(), $e->getTraceAsString());
        $this->logError($message);
    }


    /**
     * Logs an error.
     *
     * @param string $message
     */
    protected function logError($message)
    {
        $this->log($message, \Monolog\Logger::ERROR);
    }


    /**
     * Logs a message.
     *
     * @param string $message
     * @param integer $priority
     */
    protected function log($message, $level = \Monolog\Logger::INFO)
    {
        $this->getEventManager()->trigger('elvo.log', null, array(
            'message' => $message,
            'level' => $level
        ));
    }
}