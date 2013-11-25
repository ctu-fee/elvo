<?php

namespace Elvo\Mvc\Candidate;

use Elvo\Domain\Entity\Chamber;
use Elvo\Domain\Entity\Factory\CandidateFactory;
use Elvo\Domain\Entity\Collection\CandidateCollection;
use Elvo\Mvc\Authentication\Identity;


/**
 * A service responsible for loading and manipulating with candidates.
 * 
 * @todo Move candidate loading to a separate class (with adapters for array, collection, file, etc.)
 * @todo Move candidate validation to a separate class.
 */
class CandidateService
{

    /**
     * @var CandidateFactory
     */
    protected $candidateFactory;

    /**
     * @var CandidateCollection
     */
    protected $candidates;


    /**
     * Constructor.
     * 
     * @param CandidateFactory $candidateFactory
     * @param CandidateCollection|array|string $candidateData
     */
    public function __construct(CandidateFactory $candidateFactory, $candidates = null)
    {
        $this->setCandidateFactory($candidateFactory);
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