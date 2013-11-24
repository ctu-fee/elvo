<?php

namespace Elvo\Domain\Vote\Validator;

use Elvo\Domain\Entity\Vote;


/**
 * Interface for validating "vote" entities.
 */
interface ValidatorInterface
{


    /**
     * Validates the vote entity.
     * 
     * @param Vote $vote
     * @throws Exception\InvalidVoteException
     */
    public function validate(Vote $vote);
}