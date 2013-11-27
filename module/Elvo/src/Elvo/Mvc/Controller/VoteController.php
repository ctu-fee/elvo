<?php

namespace Elvo\Mvc\Controller;

use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\I18n\Translator\Translator;
use Zend\Authentication\AuthenticationService;
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;
use Elvo\Mvc\Authentication\Identity;
use Elvo\Mvc\Candidate\CandidateService;
use Elvo\Mvc\Controller\Exception\ApplicationErrorException;
use Elvo\Domain\Vote;
use Elvo\Domain\Entity;
use Elvo\Domain\Entity\Collection\CandidateCollection;
use Elvo\Domain\Vote\VoteManager;


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

    /**
     * FIXME - move to service
     * @var Entity\Factory\VoterFactory
     */
    protected $voterFactory;


    public function __construct(Vote\Service\Service $voteService, AuthenticationService $authService, 
        CandidateService $candidateService, Translator $translator)
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
     * @return Entity\Factory\VoterFactory
     */
    public function getVoterFactory()
    {
        if (! $this->voterFactory instanceof Entity\Factory\VoterFactory) {
            $this->voterFactory = new Entity\Factory\VoterFactory();
        }
        return $this->voterFactory;
    }


    /**
     * @param Entity\Factory\VoterFactory $voterFactory
     */
    public function setVoterFactory(Entity\Factory\VoterFactory $voterFactory)
    {
        $this->voterFactory = $voterFactory;
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
            
            try {
                $authService->authenticate();
            } catch (\Exception $e) {
                _dump("$e");
                _dump($_SERVER['voter_id']);
                return $this->redirect()->toRoute('autherror');
            }
        }
        
        if (! $authService->hasIdentity()) {
            _dump("unauthenticated");
            _dump($_SERVER['voter_id']);
            return $this->redirect()->toRoute('autherror');
            /* @var $response \Zend\Http\Response */
            $response = $this->getResponse();
            $response->setStatusCode(401);
            return $response;
        }
        
        $identity = $this->getIdentity();
        
        /*
         * If voting inactive, redirect to the respective page.
         */
        if (! $this->getVoteService()->isVotingActive()) {
            $event->getRouteMatch()->setParam('action', 'inactive');
        }
        
        /*
         * Early detection of voters who already voted - redirect them!
         */
        // if ($this->getVoteService()->hasAlreadyVotedById($identity->getId())) {}
        
        return parent::onDispatch($event);
    }


    public function roleAction()
    {
        $identity = $this->getIdentity();
        
        if ($this->getVoteService()->hasAlreadyVotedById($identity->getId())) {
            return $this->redirectToStatusPage();
        }
        
        /*
         * if has primary role, redirect to /form
         */
        $selectedRole = $this->params()->fromPost('role');
        if ($selectedRole) {
            try {
                $identity->setPrimaryRole($selectedRole);
                $this->redirect()->toRoute('form');
            } catch (InvalidRoleException $e) {
                _dump("$e");
                return $this->errorPage();
            }
        }
        
        $view = $this->initView();
        
        return $view;
    }


    public function formAction()
    {
        $identity = $this->getIdentity();
        
        if ($this->getVoteService()->hasAlreadyVotedById($identity->getId())) {
            return $this->redirectToStatusPage();
        }
        
        if ($identity->hasMultipleRoles() && ! $identity->getPrimaryRole()) {
            return $this->redirect()->toRoute('role');
        }
        
        $candidateService = $this->getCandidateService();
        $candidates = $candidateService->getCandidatesForIdentity($identity);
        $countRestriction = $candidateService->getCountRestrictionForIdentity($identity);
        
        $view = $this->initView(array(
            'candidates' => $candidates,
            'role' => $identity->getPrimaryRole(),
            'countRestriction' => $countRestriction,
            'csrfToken' => $this->calculateCsrfToken()
        ));
        
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
        
        if ($this->getVoteService()->hasAlreadyVotedById($identity->getId())) {
            return $this->redirectToStatusPage();
        }
        
        try {
            $this->checkCsrfToken();
            $role = $this->resolveVoterRole();
            $candidates = $this->getSubmittedCandidates();
        } catch (ApplicationErrorException $e) {
            _dump("$e");
            return $this->errorPageFromException($e);
        }
        
        if (! $this->getCandidateService()->isValidCandidateCount($identity, $candidates)) {
            return $this->errorPage('error_title_data', 'error_title_invalid_candidate_count');
        }
        
        $view = $this->initView(array(
            'candidates' => $candidates,
            'role' => $role,
            'csrfToken' => $this->calculateCsrfToken()
        ));
        
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
        
        if ($this->getVoteService()->hasAlreadyVotedById($identity->getId())) {
            return $this->redirectToStatusPage();
        }
        
        try {
            $this->checkCsrfToken();
            $role = $this->resolveVoterRole();
            $candidates = $this->getSubmittedCandidates();
        } catch (ApplicationErrorException $e) {
            _dump("$e");
            return $this->errorPageFromException($e);
        }
        
        try {
            $voter = $this->getVoterFactory()->createVoter($identity->getId(), $role);
            $this->getVoteService()->saveVote($voter, $candidates);
        } catch (\Exception $e) {
            // FIXME
            _dump("$e");
            return $this->errorPage();
        }
        
        return $this->redirectToStatusPage();
    }


    public function statusAction()
    {
        $identity = $this->getIdentity();
        
        if (! $this->getVoteService()->hasAlreadyVotedById($identity->getId())) {
            return $this->redirect()->toRoute('index');
        }
        
        $view = $this->initView();
        return $view;
    }


    public function inactiveAction()
    {
        $voteManager = $this->getVoteService()->getManager();
        $voteStatus = $voteManager->getVotingStatus();
        
        if ($voteStatus == VoteManager::STATUS_NOT_STARTED) {
            return $this->notstartedAction();
        }
        
        if ($voteStatus == VoteManager::STATUS_FINISHED) {
            return $this->finishedAction();
        }
        
        if (! $voteManager->isVotingEnabled()) {
            return $this->disabledAction();
        }
        
        $this->redirect()->toRoute('index');
    }


    public function notstartedAction()
    {
        $view = $this->initView();
        $view->setTemplate('elvo/vote/notstarted');
        return $view;
    }


    public function finishedAction()
    {
        $view = $this->initView();
        $view->setTemplate('elvo/vote/finished');
        return $view;
    }


    public function disabledAction()
    {
        $view = $this->initView();
        $view->setTemplate('elvo/vote/disabled');
        return $view;
    }
    
    /*
     * -----------------------------------------------------------------
     */
    
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
        
        $view = $this->initView(array(
            'errorTitle' => $title,
            'errorMessage' => $message
        ));
        
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


    protected function redirectToStatusPage()
    {
        return $this->redirect()->toRoute('status');
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
            _dump("$e");
            throw new ApplicationErrorException('error_title_generic', 'error_message_generic', $e);
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
            _dump('Error: no primary role');
            throw new ApplicationErrorException('error_title_generic', 'error_message_generic', $e);
        }
        
        /*
         * Get and validate the submitted voter role.
        */
        $submittedRole = $this->params()->fromPost('role');
        if (! $identity->isValidRole($submittedRole)) {
            _dump(sprintf("Invalid role '%s'", $submittedRole));
            throw new ApplicationErrorException('error_title_invalid_data', 'error_message_invalid_voter_role', $e);
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
    protected function checkCsrfToken()
    {
        $token = $this->params()->fromPost('fuu');
        $expectedToken = $this->calculateCsrfToken();
        if (! $token || $token !== $expectedToken) {
            _dump(sprintf("Invalid CSRF token [%s], expected [%s]", $token, $expectedToken));
            throw new ApplicationErrorException('error_title_generic', 'error_message_generic');
        }
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
}