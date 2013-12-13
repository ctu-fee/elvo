<?php

namespace Elvo\Domain\Entity\Collection;

use Elvo\Domain\Entity\Vote;
use Elvo\Domain\Entity\VoterRole;


/**
 * A collection of "vote" entities.
 */
class VoteCollection extends AbstractCollection
{


    /**
     * Returns the number of votes for a specific voter role.
     * 
     * @param VoterRole $voterRole
     * @return integer
     */
    public function countByVoterRole(VoterRole $voterRole)
    {
        $count = 0;
        foreach ($this->items as $vote) {
            /* @var $vote \Elvo\Domain\Entity\Vote */
            if ($vote->hasVoterRole($voterRole)) {
                $count ++;
            }
        }
        
        return $count;
    }


    /**
     * {@inhertidoc}
     * @see \Elvo\Domain\Entity\Collection\AbstractCollection::validate()
     */
    protected function validate($vote)
    {
        if (! $vote instanceof Vote) {
            $this->throwInvalidItemException($vote);
        }
    }
}