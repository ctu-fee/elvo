<?php

namespace Elvo\Domain\Entity\Collection;

use Elvo\Domain\Entity\CandidateResult;
use Elvo\Domain\Entity\Candidate;


/**
 * Collection of CandidateResult entities.
 */
class CandidateResultCollection extends AbstractCollection
{


    /**
     * Add one vote to the result of the corresponding candidate.
     * 
     * @param Candidate $candidate
     */
    public function addVoteToCandidate(Candidate $candidate)
    {
        $candidateResult = $this->findByCandidate($candidate);
        if (! $candidateResult) {
            $candidateResult = $this->createCandidateResult($candidate);
            $this->append($candidateResult);
        }
        
        $candidateResult->addOne();
    }


    /**
     * Finds the result for the corresponding candidate.
     * 
     * @param Candidate $candidate
     * @return CandidateResult|null
     */
    public function findByCandidate(Candidate $candidate)
    {
        return $this->findByCandidateId($candidate->getId());
    }


    /**
     * Finds the result for the candidate with the provided ID.
     * 
     * @param integer $candidateId
     * @return CandidateResult|null
     */
    public function findByCandidateId($candidateId)
    {
        foreach ($this->items as $candidateResult) {
            if ($candidateId == $candidateResult->getCandidate()->getId()) {
                return $candidateResult;
            }
        }
        
        return null;
    }


    /**
     * Sorts the collection by the number of votes for each CandidateResult.
     * 
     * @return number
     */
    public function sortByNumVotes()
    {
        $this->items->uasort(function (CandidateResult $cr1, CandidateResult $cr2)
        {
            $numVotes1 = $cr1->getNumVotes();
            $numVotes2 = $cr2->getNumVotes();
            
            if ($numVotes1 == $numVotes2) {
                return 0;
            }
            
            return ($numVotes1 < $numVotes2) ? 1 : - 1;
        });
    }


    /**
     * Creates a candidate result for the provided candidate.
     * 
     * @param Candidate $candidate
     * @return CandidateResult
     */
    protected function createCandidateResult(Candidate $candidate)
    {
        return new CandidateResult($candidate);
    }


    /**
     * {@inheritdoc}
     * @see \Elvo\Domain\Entity\Collection\AbstractCollection::validate()
     */
    protected function validate($candidateResult)
    {
        if (! $candidateResult instanceof CandidateResult) {
            $this->throwInvalidItemException($candidateResult);
        }
    }
}