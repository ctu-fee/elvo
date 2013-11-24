<?php

namespace Elvo\Domain\Vote\Storage;

use Elvo\Domain\Entity\EncryptedVote;


/**
 * The interface defines a storage for encrypted votes.
 */
interface StorageInterface
{


    /**
     * Stores a vote.
     * 
     * @param EncryptedVote $encryptedVote
     */
    public function save(EncryptedVote $encryptedVote);


    /**
     * Returns all votes.
     * 
     * @return \Elvo\Domain\Entity\Collection\EncryptedVoteCollection
     */
    public function fetchAll();
}