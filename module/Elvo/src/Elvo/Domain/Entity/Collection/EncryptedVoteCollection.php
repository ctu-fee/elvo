<?php

namespace Elvo\Domain\Entity\Collection;

use Elvo\Domain\Entity\EncryptedVote;
use Elvo\Domain\Entity\Collection\AbstractCollection;


/**
 * A collection of "EncryptedVote" entities.
 */
class EncryptedVoteCollection extends AbstractCollection
{


    /**
     * {@inhertidoc}
     * @see \Elvo\Domain\Entity\Collection\AbstractCollection::validate()
     */
    protected function validate($encryptedVote)
    {
        if (! $encryptedVote instanceof EncryptedVote) {
            $this->throwInvalidItemException($encryptedVote);
        }
    }
}