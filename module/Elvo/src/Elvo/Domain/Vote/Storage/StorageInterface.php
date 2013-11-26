<?php

namespace Elvo\Domain\Vote\Storage;

use Elvo\Domain\Entity\EncryptedVote;
use Elvo\Domain\Entity\Voter;


/**
 * The interface defines a storage for encrypted votes.
 */
interface StorageInterface
{


    /**
     * Remember that the voter has already voted.
     * 
     * @param string $voterId
     */
    public function saveVoterId($voterId);


    /**
     * Returns true, if the provided voter ID exists in the storage.
     * 
     * @param string $voterId
     */
    public function existsVoterId($voterId);


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