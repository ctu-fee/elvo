<?php

namespace Elvo\Mvc\Controller;

use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\I18n\Translator\Translator;
use Zend\Authentication\AuthenticationService;
use Elvo\Mvc\Authentication\Identity;
use Elvo\Mvc\Candidate\CandidateService;
use Elvo\Domain\Vote;
use Elvo\Domain\Entity;
use Elvo\Domain\Entity\Collection\CandidateCollection;


class VoteController extends AbstractActionController
{

    /**
     * @var Vote\Service\Service
     */
    protected $voteService;

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


    public function __construct(Vote\Service\Service $voteService, AuthenticationService $authService, CandidateService $candidateService, Translator $translator)
    {
        $this->setVoteService($voteService);
        $this->setAuthService($authService);
        $this->setCandidateService($candidateService);
        $this->setTranslator($translator);
    }


    /**
     * @return Vote\Service\Service
     */
    public function getVoteService()
    {
        return $this->voteService;
    }


    /**
     * @param Vote\Service\Service $voteService
     */
    public function setVoteService(Vote\Service\Service $voteService)
    {
        $this->voteService = $voteService;
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
        // check if the voting is active, if not - redirect to index
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
        
        /*
         * Get and validate the submitted voter role.
         */
        $submittedRole = $this->params()->fromPost('role');
        if (! $identity->isValidRole($submittedRole)) {
            return $this->errorPage('error_title_invalid_data', 'error_message_invalid_voter_role');
        }
        $role = $submittedRole;
        
        /*
         * Get and validate submitted candidates.
         */
        $candidates = $this->getSubmittedCandidates();
        
        $view = new ViewModel(array(
            'candidates' => $candidates,
            'role' => $role
        ));
        $view->addChild($this->createNavbarViewModel(), 'mainNavbar');
        
        return $view;
    }


    public function submitAction()
    {
        $identity = $this->getIdentity();
        
        /*
         * Get and validate the submitted voter role.
        */
        $submittedRole = $this->params()->fromPost('role');
        if (! $identity->isValidRole($submittedRole)) {
            return $this->errorPage('error_title_invalid_data', 'error_message_invalid_voter_role');
        }
        $role = $submittedRole;
        
        /*
         * Get and validate submitted candidates.
        */
        $candidates = $this->getSubmittedCandidates();
        
        // save the vote
        try {
            $voter = new Entity\Voter($identity->getId(), new Entity\VoterRole($role));
        } catch (\Exception $e) {
            throw $e;
        }
        
        try {
            $this->getVoteService()->saveVote($voter, $candidates);
        } catch (\Exception $e) {
            throw $e;
        }
        // redirect
        return $this->redirect()->toRoute('status');
    }


    public function statusAction()
    {
        // check if the user has voted
        // - if voted - show status page
        // - else - redirect to start
        $view = new ViewModel();
        
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
     * Retrieves and validates the candidates submitted by the voter.
     * 
     * @return CandidateCollection
     */
    protected function getSubmittedCandidates()
    {
        $identity = $this->getIdentity();
        $submittedCandidateIds = $this->params()->fromPost('candidates');
        if (! is_array($submittedCandidateIds)) {
            $submittedCandidateIds = array();
        }
        $candidates = $this->getCandidateService()->getCandidatesForIdentityFilteredByIds($identity, $submittedCandidateIds);
        
        return $candidates;
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