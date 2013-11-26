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
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;
use Elvo\Mvc\Controller\Exception\ApplicationErrorException;


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
        if ($identity->hasMultipleRoles() && ! $identity->getPrimaryRole()) {
            return $this->redirect()->toRoute('role');
        }
        
        $candidateService = $this->getCandidateService();
        $candidates = $candidateService->getCandidatesForIdentity($identity);
        $countRestriction = $candidateService->getCountRestrictionForIdentity($identity);
        
        $view = new ViewModel(array(
            'candidates' => $candidates,
            'role' => $identity->getPrimaryRole(),
            'countRestriction' => $countRestriction,
            'csrfToken' => $this->calculateCsrfToken()
        ));
        $view->addChild($this->createNavbarViewModel(), 'mainNavbar');
        
        return $view;
    }


    /**
     * /confirm
     * 
     * @return mixed
     */
    public function confirmAction()
    {
        $identity = $this->getIdentity();
        
        try {
            $this->checkCsrfToken();
            $role = $this->resolveVoterRole();
            $candidates = $this->getSubmittedCandidates();
        } catch (ApplicationErrorException $e) {
            return $this->errorPageFromException($e);
        }
        
        if (! $this->getCandidateService()->isValidCandidateCount($identity, $candidates)) {
            return $this->errorPage('error_title_data', 'error_title_invalid_candidate_count');
        }
        
        $view = new ViewModel(array(
            'candidates' => $candidates,
            'role' => $role,
            'csrfToken' => $this->calculateCsrfToken()
        ));
        $view->addChild($this->createNavbarViewModel(), 'mainNavbar');
        
        return $view;
    }


    /**
     * /submit
     * 
     * @return mixed
     */
    public function submitAction()
    {
        $identity = $this->getIdentity();
        
        try {
            $this->checkCsrfToken();
            $role = $this->resolveVoterRole();
            $candidates = $this->getSubmittedCandidates();
        } catch (ApplicationErrorException $e) {
            return $this->errorPageFromException($e);
        }
        
        try {
            $voter = new Entity\Voter($identity->getId(), new Entity\VoterRole($role));
            $this->getVoteService()->saveVote($voter, $candidates);
        } catch (\Exception $e) {
            throw $e;
        }
        
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


    /**
     * Returns an error page view model.
     * 
     * @param string $title
     * @param string $message
     * @return ViewModel
     */
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


    /**
     * Returns an error page view model based on an exception.
     * 
     * @param ApplicationErrorException $e
     * @return ViewModel
     */
    protected function errorPageFromException(ApplicationErrorException $e)
    {
        return $this->errorPage($e->getErrorTitle(), $e->getErrorMessage());
    }
    
    /*
     * 
     */
    
    /**
     * Returns the main navigation bar's view model.
     * @return ViewModel
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
        
        try {
            $candidates = $this->getCandidateService()->getCandidatesForIdentityFilteredByIds($identity, $submittedCandidateIds);
        } catch (\Exception $e) {
            throw new ApplicationErrorException('error_title_generic', 'error_message_generic');
        }
        
        return $candidates;
    }


    /**
     * Fetch and validate the voter's role.
     * 
     * @throws ApplicationErrorException
     * @return string
     */
    protected function resolveVoterRole()
    {
        $identity = $this->getIdentity();
        if (! $identity->getPrimaryRole()) {
            throw new ApplicationErrorException('error_title_generic', 'error_message_generic');
        }
        
        /*
         * Get and validate the submitted voter role.
        */
        $submittedRole = $this->params()->fromPost('role');
        if (! $identity->isValidRole($submittedRole)) {
            throw new ApplicationErrorException('error_title_invalid_data', 'error_message_invalid_voter_role');
        }
        
        return $submittedRole;
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
     * "Shortcut" for the translator call.
     * 
     * @param string $message
     * @param string $textDomain
     * @param string $locale
     * @return string
     */
    protected function translate($message, $textDomain = null, $locale = null)
    {
        return $this->getTranslator()->translate($message, $textDomain, $locale);
    }


    /**
     * Calculate simple CSRF token based on the user's identity.
     * 
     * @return string
     */
    protected function calculateCsrfToken()
    {
        return md5($this->getIdentity()->getId());
    }


    /**
     * Checks if the POST-ed token corresponds to the calculated one.
     * 
     * @throws ApplicationErrorException
     */
    public function checkCsrfToken()
    {
        $token = $this->params()->fromPost('fuu');
        if (! $token || $token !== $this->calculateCsrfToken()) {
            throw new ApplicationErrorException('error_title_generic', 'error_message_generic');
        }
    }
}