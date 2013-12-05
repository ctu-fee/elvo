<?php

namespace Elvo\Domain\Candidate\Service;

use Elvo\Domain\Entity\Chamber;
use Elvo\Domain\Entity\Factory\CandidateFactory;
use Elvo\Domain\Entity\Collection\CandidateCollection;
use Elvo\Mvc\Authentication\Identity;
use Elvo\Util\Options;
use Elvo\Domain\Vote\VoteManager;


/**
 * A service responsible for loading and manipulating with candidates.
 * 
 * @todo Move candidate loading to a separate class (with adapters for array, collection, file, etc.)
 * @todo Move candidate validation to a separate class.
 */
class Service
{

    const OPT_CANDIDATES = 'candidates';

    /**
     * @var Options
     */
    protected $options;

    /**
     * @var CandidateFactory
     */
    protected $candidateFactory;

    /**
     * @var VoteManager
     */
    protected $voteManager;

    /**
     * @var CandidateCollection
     */
    protected $candidates;


    /**
     * Constructor.
     * 
     * @param CandidateFactory $candidateFactory
     * @param VoteManager $voteManager
     * @param Options $options
     */
    public function __construct(CandidateFactory $candidateFactory, VoteManager $voteManager, Options $options)
    {
        $this->options = $options;
        $this->setCandidateFactory($candidateFactory);
        $this->setVoteManager($voteManager);
        
        $candidates = $this->options->get(self::OPT_CANDIDATES);
        if (null !== $candidates) {
            $this->setCandidates($candidates);
        }
    }


    /**
     * @return CandidateFactory
     */
    public function getCandidateFactory()
    {
        return $this->candidateFactory;
    }


    /**
     * @param CandidateFactory $candidateFactory
     */
    public function setCandidateFactory(CandidateFactory $candidateFactory)
    {
        $this->candidateFactory = $candidateFactory;
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
     * @return CandidateCollection
     */
    public function getCandidates()
    {
        return $this->candidates;
    }


    /**
     * @param CandidateCollection|array|string $candidates
     */
    public function setCandidates($candidates)
    {
        if (! $candidates instanceof CandidateCollection) {
            if (is_string($candidates) && is_file($candidates)) {
                $candidates = $this->loadCandidatesFromFile($candidates);
            }
            
            if (! is_array($candidates)) {
                throw new Exception\InvalidCandidateDataException('Expected CandidateCollection or array');
            }
            
            $this->validateCandidateArray($candidates);
            
            $collection = new CandidateCollection();
            foreach ($candidates as $item) {
                $collection->append($this->getCandidateFactory()
                    ->createCandidate($item));
            }
            
            $candidates = $collection;
        }
        
        $this->candidates = $candidates;
    }


    /**
     * Returns candidates for the specific chamber only.
     * 
     * @param Chamber $chamber
     * @return CandidateCollection
     */
    public function getCandidatesForChamber(Chamber $chamber)
    {
        $candidatesForChamber = new CandidateCollection();
        foreach ($this->getCandidates() as $candidate) {
            if ($chamber->getCode() === $candidate->getChamber()->getCode()) {
                $candidatesForChamber->append($candidate);
            }
        }
        
        return $candidatesForChamber;
    }


    /**
     * Returns the candidates from the chamber, the user has role to vote for.
     * 
     * @param Identity $identity
     * @return CandidateCollection
     */
    public function getCandidatesForIdentity(Identity $identity)
    {
        $chamber = new Chamber($identity->getPrimaryRole());
        return $this->getCandidatesForChamber($chamber);
    }


    /**
     * Returns selected candidates from the candidates relevant for the voter.
     * 
     * @param Identity $identity
     * @param array $candidateIds
     * @return CandidateCollection
     */
    public function getCandidatesForIdentityFilteredByIds(Identity $identity, array $candidateIds)
    {
        $filteredCandidates = new CandidateCollection();
        if (empty($candidateIds)) {
            return $filteredCandidates;
        }
        
        foreach ($candidateIds as $id) {
            $candidate = $this->candidates->findById($id);
            if (null === $candidate) {
                throw new Exception\CandidateNotFoundException(sprintf("Candidate with ID:%d not found", $id));
            }
            
            if ($identity->getPrimaryRole() != $candidate->getChamber()->getCode()) {
                throw new Exception\InvalidCandidateDataException(sprintf("Candidate ID:%d for chamber '%s' cannot be selected by voter with role '%s'", $id, $candidate->getChamber(), $identity->getPrimaryRole()));
            }
            
            $filteredCandidates->append($candidate);
        }
        
        return $filteredCandidates;
    }


    public function getCountRestrictionForIdentity(Identity $identity)
    {
        $role = $identity->getPrimaryRole();
        $chamber = new Chamber($role);
        
        return $this->getVoteManager()->getMaxVotesForChamber($chamber);
    }


    public function isValidCandidateCount(Identity $identity, CandidateCollection $candidates)
    {
        $countRestriction = $this->getCountRestrictionForIdentity($identity);
        if ($candidates->count() > $countRestriction) {
            return false;
        }
        
        return true;
    }


    /**
     * Validates the list of candidates.
     * 
     * @todo Move to a separate class.
     * 
     * @param array $candidateArray
     * @throws Exception\InvalidCandidateDataException
     */
    protected function validateCandidateArray(array $candidateArray)
    {
        foreach ($candidateArray as $item) {
            if (! isset($item['id'])) {
                throw new Exception\InvalidCandidateDataException('Each candidate item must have the "id" property set');
            }
        }
    }


    /**
     * Load the candidate list from a file.
     * 
     * @param string $path
     * @return array
     */
    protected function loadCandidatesFromFile($path)
    {
        return require $path;
    }
}