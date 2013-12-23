<?php

namespace Elvo\Domain\Candidate\Service;

use Elvo\Mvc\Authentication\Identity;
use Elvo\Domain\Entity\Collection\CandidateCollection;


interface ServiceInterface
{


    /**
     * Returns the candidates from the chamber, the user has role to vote for.
     *
     * @param Identity $identity
     * @return CandidateCollection
     */
    public function getCandidatesForIdentity(Identity $identity);


    /**
     * Returns selected candidates from the candidates relevant for the voter.
     *
     * @param Identity $identity
     * @param array $candidateIds
     * @return CandidateCollection
     */
    public function getCandidatesForIdentityFilteredByIds(Identity $identity, array $candidateIds);


    /**
     * Returns the maximum number of candidates that can be elected for the chamber the current identity
     * is enabled to vote for.
     * 
     * @param Identity $identity
     */
    public function getCountRestrictionForIdentity(Identity $identity);


    /**
     * Returns the maximum votes per voter for the corresponding chamber.
     * 
     * @param Identity $identity
     */
    public function getVoteRestrictionForIdentity(Identity $identity);


    /**
     * Checks, if the provided candidate count doesn't exceed the maximum value allowed.
     * 
     * @param Identity $identity
     * @param CandidateCollection $candidates
     */
    public function isValidCandidateCount(Identity $identity, CandidateCollection $candidates);
}