<?php

namespace Elvo\Domain\Entity\Collection;

use Elvo\Domain\Entity\Vote;


/**
 * A collection of "vote" entities.
 */
class VoteCollection extends AbstractCollection
{


    /**
     * Appends a "vote" entity to the collection.
     * 
     * @param Vote $vote
     */
    public function append($vote)
    {
        if (! $vote instanceof Vote) {
            $this->throwInvalidItemException($vote);
        }
        
        parent::append($vote);
    }
}