<?php

namespace Elvo\Domain\Entity\Collection;

use Elvo\Domain\Entity\Vote;


/**
 * A collection of "vote" entities.
 */
class VoteCollection extends AbstractCollection
{


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