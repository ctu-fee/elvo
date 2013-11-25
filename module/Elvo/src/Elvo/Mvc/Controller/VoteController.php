<?php

namespace Elvo\Mvc\Controller;

use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\I18n\Translator\Translator;
use Zend\Mvc\MvcEvent;
use Zend\Authentication\AuthenticationService;
use Elvo\Mvc\Authentication\Identity;
use Elvo\Mvc\Candidate\CandidateService;
use Elvo\Domain\Entity\Chamber;


class VoteController extends AbstractActionController
{

    /**
     * @var AuthenticationService
     */
    protected $authAdapter;

    /**
     * @var CandidateService
     */
    protected $candidateService;

    /**
     * @var Translator
     */
    protected $translator;


    public function __construct(AuthenticationService $authService, CandidateService $candidateService, Translator $translator)
    {
        $this->setAuthService($authService);
        $this->setCandidateService($candidateService);
        $this->setTranslator($translator);
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


    /**
     * @return CandidateService
     */
    public function getCandidateService()
    {
        return $this->candidateService;
    }


    /**
     * @param CandidateService $candidateService
     */
    public function setCandidateService(CandidateService $candidateService)
    {
        $this->candidateService = $candidateService;
    }


    /**
     * @return Translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }


    /**
     * @param Translator $translator
     */
    public function setTranslator(Translator $translator)
    {
        $this->translator = $translator;
    }


    /**
     * {@inheritdoc}
     * @see \Zend\Mvc\Controller\AbstractActionController::onDispatch()
     */
    public function onDispatch(MvcEvent $event)
    {
        $authService = $this->getAuthService();
        if (! $authService->hasIdentity()) {
            $authService->authenticate();
        }
        
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
        $identity = $this->getIdentity();
        $selectedRole = $this->params()->fromPost('role');
        if ($selectedRole) {
            try {
                $identity->setPrimaryRole($selectedRole);
                $this->redirect()->toRoute('form');
            } catch (InvalidRoleException $e) {
                _dump("$e");
            }
        }
        
        $view = new ViewModel();
        
        $view->addChild($this->createNavbarViewModel(), 'mainNavbar');
        
        return $view;
    }


    public function formAction()
    {
        $identity = $this->getIdentity();
        /*
         * Check user roles - if more than one, check if one of them has been selected and if
         * none is selected, redirect to /role, where the user will select the role to vote.
         */
        
        // $selectedRole = $this->params()->fromPost('role');
        if ($identity->hasMultipleRoles() && ! $identity->getPrimaryRole()) {
            return $this->redirect()->toRoute('role');
        }
        
        $candidates = $this->getCandidateService()->getCandidatesForIdentity($identity);
        
        $view = new ViewModel(array(
            'candidates' => $candidates,
            'role' => $identity->getPrimaryRole()
        ));
        $view->addChild($this->createNavbarViewModel(), 'mainNavbar');
        
        return $view;
    }


    public function confirmAction()
    {
        $identity = $this->getIdentity();
        
        $submittedCandidateIds = $this->params()->fromPost('candidates');
        $submittedRole = $this->params()->fromPost('role');
        
        return $this->errorPage();
        
        _dump($candidates);
        _dump($role);
        
        $candidates = $this->getCandidateService()->getCandidatesForIdentity($identity);
        
        $view = new ViewModel(array(
            'candidates' => $candidates,
            'role' => $role
        ));
        $view->addChild($this->createNavbarViewModel(), 'mainNavbar');
        
        return $view;
    }


    protected function errorPage($title = null, $message = null)
    {
        if (null === $title) {
            $title = 'error_title_generic';
        }
        
        if (null === $message) {
            $message = 'error_message_generic';
        }
        
        $view = new ViewModel(array(
            'errorTitle' => $title,
            'errorMessage' => $message
        ));
        
        $view->addChild($this->createNavbarViewModel(), 'mainNavbar');
        $view->setTemplate('elvo/vote/error');
        
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


    /**
     * Returns the current user's identity.
     * 
     * @return Identity
     */
    protected function getIdentity()
    {
        return $this->getAuthService()->getIdentity();
    }


    protected function translate($message, $textDomain = null, $locale = null)
    {
        return $this->getTranslator()->translate($message, $textDomain, $locale);
    }
}