<?php

namespace Elvo\Domain\Vote\Validator;

use Elvo\Domain\Entity\Vote;
use Elvo\Util\Exception\MissingOptionException;


/**
 * Validates if the number of candidates in the vote is under the 
 * required limit.
 * 
 * Required options:
 * - "max_votes_count" - an array, where the keys represent "chamber" types and values
 * represent maximum candidates can be in a single vote.
 */
class CandidateCountValidator extends AbstractValidator
{

    const OPT_MAX_VOTES_COUNT = 'max_votes_count';


    /**
     * {@inhertidoc}
     * @see \Elvo\Domain\Vote\Validator\ValidatorInterface::validate()
     */
    public function validate(Vote $vote)
    {
        $chamberCountValue = $this->getChamberCountValue();
        $chamberType = $vote->getVoterRole()->getValue();
        
        if (! isset($chamberCountValue[$chamberType])) {
            throw new Exception\InvalidVoteException(sprintf("Count limit for chamber '%s' is unavailable", $chamberType));
        }
        
        $countLimit = intval($chamberCountValue[$chamberType]);
        $candidateCount = $vote->getCandidates()->count();
        
        if ($candidateCount > $countLimit) {
            throw new Exception\CandidateCountExceededException(sprintf("The vote contains %d candidates for chamber '%s', count limit is %d", $candidateCount, $chamberType, $countLimit));
        }
    }


    protected function getChamberCountValue()
    {
        $chamberCountValue = $this->options->get(self::OPT_MAX_VOTES_COUNT);
        if (null === $chamberCountValue) {
            throw new MissingOptionException(sprintf("Missing option '%s'", self::OPT_MAX_VOTES_COUNT));
        }
        
        return $chamberCountValue;
    }
}