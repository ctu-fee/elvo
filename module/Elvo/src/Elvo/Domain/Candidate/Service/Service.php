<?php

namespace Elvo\Domain\Candidate\Service;

use Elvo\Mvc\Authentication\Identity;
use Elvo\Util\Options;
use Elvo\Domain\Candidate;
use Elvo\Domain\Entity\Chamber;
use Elvo\Domain\Entity\Collection\CandidateCollection;
use Elvo\Domain\Vote\VoteManager;


/**
 * A service responsible for loading and manipulating with candidates.
 * 
 * @todo Move candidate validation to a separate class.
 */
class Service implements ServiceInterface
{

    const OPT_CANDIDATES = 'candidates';

    /**
     * @var Options
     */
    protected $options;

    /**
     * @var VoteManager
     */
    protected $voteManager;

    /**
     * @var Candidate\Storage\StorageInterface
     */
    protected $candidateStorage;


    /**
     * Constructor.
     * 
     * @param Candidate\Storage\StorageInterface $candidateStorage
     * @param VoteManager $voteManager
     */
    public function __construct(Candidate\Storage\StorageInterface $candidateStorage, VoteManager $voteManager)
    {
        $this->setCandidateStorage($candidateStorage);
        $this->setVoteManager($voteManager);
    }


    /**
     * @return Candidate\Storage\StorageInterface
     */
    public function getCandidateStorage()
    {
        return $this->candidateStorage;
    }


    /**
     * @param Candidate\Storage\StorageInterface $candidateStorage
     */
    public function setCandidateStorage(Candidate\Storage\StorageInterface $candidateStorage)
    {
        $this->candidateStorage = $candidateStorage;
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
     * Returns candidates for the specific chamber only.
     * 
     * @param Chamber $chamber
     * @return CandidateCollection
     */
    public function fetchCandidatesForChamber(Chamber $chamber)
    {
        $candidateCollection = $this->fetchCandidates();
        $candidatesForChamber = new CandidateCollection();
        foreach ($candidateCollection as $candidate) {
            if ($chamber->getCode() === $candidate->getChamber()->getCode()) {
                $candidatesForChamber->append($candidate);
            }
        }
        
        return $candidatesForChamber;
    }


    /**
     * {@inheritdoc}
     * @see \Elvo\Domain\Candidate\Service\ServiceInterface::getCandidatesForIdentity()
     */
    public function getCandidatesForIdentity(Identity $identity)
    {
        $chamber = new Chamber($identity->getPrimaryRole());
        return $this->fetchCandidatesForChamber($chamber);
    }


    /**
     * {@inheritdoc}
     * @see \Elvo\Domain\Candidate\Service\ServiceInterface::getCandidatesForIdentityFilteredByIds()
     */
    public function getCandidatesForIdentityFilteredByIds(Identity $identity, array $candidateIds)
    {
        $filteredCandidates = new CandidateCollection();
        if (empty($candidateIds)) {
            return $filteredCandidates;
        }
        
        foreach ($candidateIds as $id) {
            $candidate = $this->fetchCandidates()->findById($id);
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


    /**
     * {@inheritdoc}
     * @see \Elvo\Domain\Candidate\Service\ServiceInterface::getCountRestrictionForIdentity()
     */
    public function getCountRestrictionForIdentity(Identity $identity)
    {
        $role = $identity->getPrimaryRole();
        $chamber = new Chamber($role);
        
        return $this->getVoteManager()->getMaxCandidatesForChamber($chamber);
    }


    /**
     * {@inheritdoc}
     * @see \Elvo\Domain\Candidate\Service\ServiceInterface::isValidCandidateCount()
     */
    public function isValidCandidateCount(Identity $identity, CandidateCollection $candidates)
    {
        $countRestriction = $this->getCountRestrictionForIdentity($identity);
        if ($candidates->count() > $countRestriction) {
            return false;
        }
        
        return true;
    }


    /**
     * Fetches the candidates from the storage.
     * 
     * @return CandidateCollection
     */
    protected function fetchCandidates()
    {
        return $this->getCandidateStorage()->fetchAll();
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