<?php

namespace Elvo\Domain\Vote;

use Elvo\Domain\Entity\EncryptedVote;
use Elvo\Domain\Entity\Vote;


/**
 * Interface for encryption/decryption of votes.
 */
interface EncryptorInterface
{


    /**
     * Encrypts the vote.
     * 
     * @param Vote $vote
     * @return EncryptedVote
     */
    public function encryptVote(Vote $vote);


    /**
     * Decrypts the vote.
     * 
     * @param EncryptedVote $encryptedVote
     * @return Vote
     */
    public function decryptVote(EncryptedVote $encryptedVote);
}