<?php

namespace Elvo\Domain\Vote\Service;

use Elvo\Domain\Vote;
use Elvo\Domain\Entity;
use Elvo\Domain\Entity\Factory\VoteFactoryInterface;
use Elvo\Domain\Entity\Collection\CandidateCollection;
use Elvo\Domain\Entity\Collection\VoteCollection;


/**
 * Vote service class implementing business logic around voting.
 */
class Service implements ServiceInterface
{

    /**
     * @var Vote\VoteManager
     */
    protected $manager;

    /**
     * @var VoteFactoryInterface
     */
    protected $factory;

    /**
     * @var Vote\Validator\ValidatorInterface
     */
    protected $validator;

    /**
     * @var Vote\EncryptorInterface
     */
    protected $encryptor;

    /**
     * @var Vote\Storage\StorageInterface
     */
    protected $storage;


    /**
     * Constructor.
     * 
     * @param VoteFactoryInterface $factory
     * @param Vote\Validator\ValidatorInterface $validator
     * @param Vote\EncryptorInterface $encryptor
     * @param Vote\Storage\StorageInterface $storage
     */
    public function __construct(Vote\VoteManager $manager, VoteFactoryInterface $factory, 
        Vote\Validator\ValidatorInterface $validator, Vote\EncryptorInterface $encryptor, 
        Vote\Storage\StorageInterface $storage)
    {
        $this->setManager($manager);
        $this->setFactory($factory);
        $this->setValidator($validator);
        $this->setEncryptor($encryptor);
        $this->setStorage($storage);
    }


    /**
     * @return Vote\VoteManager
     */
    public function getManager()
    {
        return $this->manager;
    }


    /**
     * @param Vote\VoteManager $manager
     */
    public function setManager(Vote\VoteManager $manager)
    {
        $this->manager = $manager;
    }


    /**
     * @return VoteFactoryInterface
     */
    public function getFactory()
    {
        return $this->factory;
    }


    /**
     * @param VoteFactoryInterface $factory
     */
    public function setFactory(VoteFactoryInterface $factory)
    {
        $this->factory = $factory;
    }


    /**
     * @return Vote\Validator\ValidatorInterface
     */
    public function getValidator()
    {
        return $this->validator;
    }


    /**
     * @param Vote\Validator\ValidatorInterface $validator
     */
    public function setValidator(Vote\Validator\ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }


    /**
     * @return Vote\EncryptorInterface
     */
    public function getEncryptor()
    {
        return $this->encryptor;
    }


    /**
     * @param Vote\EncryptorInterface $encryptor
     */
    public function setEncryptor(Vote\EncryptorInterface $encryptor)
    {
        $this->encryptor = $encryptor;
    }


    /**
     * @return Vote\Storage\StorageInterface
     */
    public function getStorage()
    {
        return $this->storage;
    }


    /**
     * @param Vote\Storage\StorageInterface $storage
     */
    public function setStorage(Vote\Storage\StorageInterface $storage)
    {
        $this->storage = $storage;
    }


    /**
     * {@inheritdoc}
     * @see \Elvo\Domain\Vote\Service\ServiceInterface::isVotingActive()
     */
    public function isVotingActive()
    {
        return $this->getManager()->isVotingActive();
    }


    /**
     * {@inheritdoc}
     * @see \Elvo\Domain\Vote\Service\ServiceInterface::hasAlreadyVoted()
     */
    public function hasAlreadyVoted(Entity\Voter $voter)
    {
        return $this->hasAlreadyVotedById($voter->getId());
    }


    /**
     * {@inhertidoc}
     * @see \Elvo\Domain\Vote\Service\ServiceInterface::hasAlreadyVotedById()
     */
    public function hasAlreadyVotedById($voterId)
    {
        return $this->getStorage()->existsVoterId($voterId);
    }


    /**
     * {@inheritdoc}
     * @see \Elvo\Domain\Vote\Service\ServiceInterface::saveVote()
     */
    public function saveVote(Entity\Voter $voter, CandidateCollection $candidates)
    {
        $this->checkVotingActive();
        $this->checkHasAlreadyVoted($voter);
        
        $vote = $this->createVote($voter, $candidates);
        $this->validateVote($vote);
        
        $encryptedVote = $this->encryptVote($vote);

        $this->storeVote($voter, $encryptedVote);
    }


    /**
     * {@inheritdoc}
     * @see \Elvo\Domain\Vote\Service\ServiceInterface::fetchAllVotes()
     */
    public function fetchAllVotes()
    {
        $encryptedVotes = $this->fetchAllEncryptedVotes();
        
        $voteCollection = new VoteCollection();
        foreach ($encryptedVotes as $encryptedVote) {
            $voteCollection->append($this->decryptVote($encryptedVote));
        }
        
        return $voteCollection;
    }


    /**
     * {@inhertidoc}
     * @see \Elvo\Domain\Vote\Service\ServiceInterface::countVotes()
     */
    public function countVotes()
    {
        return $this->getStorage()->count();
    }


    /**
     * Checks if the voting is active. If not, throws an exception.
     * 
     * @throws Exception\VotingInactiveException
     */
    public function checkVotingActive()
    {
        if (! $this->isVotingActive()) {
            throw new Exception\VotingInactiveException('Voting is currently inactive');
        }
    }


    /**
     * Checks if the voter has already voted.
     * 
     * @param Entity\Voter $voter
     * @throws Exception\VoterAlreadyVotedException
     */
    public function checkHasAlreadyVoted(Entity\Voter $voter)
    {
        if ($this->hasAlreadyVoted($voter)) {
            throw new Exception\VoterAlreadyVotedException(
                sprintf("Voter with ID '%s' has already voted", $voter->getId()));
        }
    }


    /**
     * Creates a vote entity out of a voter entity and candidate collection.
     * 
     * @param Entity\Voter $voter
     * @param CandidateCollection $candidates
     * @throws Exception\VoteCreationException
     * @return Vote
     */
    public function createVote(Entity\Voter $voter, CandidateCollection $candidates)
    {
        try {
            $vote = $this->getFactory()->createVote($voter, $candidates);
        } catch (\Exception $e) {
            throw new Exception\VoteCreationException($this->formatExceptionMessage($e, 'Cannot create vote'), null, $e);
        }
        
        return $vote;
    }


    /**
     * Validates a vote.
     * 
     * @param Entity\Vote $vote
     * @throws Exception\VoteValidationException
     */
    public function validateVote(Entity\Vote $vote)
    {
        try {
            $this->getValidator()->validate($vote);
        } catch (\Exception $e) {
            throw new Exception\VoteValidationException($this->formatExceptionMessage($e, 'Vote validation exception'), 
                null, $e);
        }
    }


    /**
     * Encrypts a vote.
     * 
     * @param Entity\Vote $vote
     * @throws Exception\VoteEncryptionException
     * @return Entity\EncryptedVote
     */
    public function encryptVote(Entity\Vote $vote)
    {
        try {
            $encryptedVote = $this->getEncryptor()->encryptVote($vote);
        } catch (\Exception $e) {
            throw new Exception\VoteEncryptionException(
                $this->formatExceptionMessage($e, 'Exception during vote encryption'), null, $e);
        }
        
        return $encryptedVote;
    }


    /**
     * Decrypts an encrypted vote.
     * 
     * @param Entity\EncryptedVote $vote
     * @throws Exception\VoteEncryptionException
     * @return Entity\Vote
     */
    public function decryptVote(Entity\EncryptedVote $encryptedVote)
    {
        try {
            $vote = $this->getEncryptor()->decryptVote($encryptedVote);
        } catch (\Exception $e) {
            throw new Exception\VoteEncryptionException(
                $this->formatExceptionMessage($e, 'Exception during vote decryption'), null, $e);
        }
        
        return $vote;
    }


    /**
     * Stores the encrypted vote and the corresponding voter in a transaction.
     * 
     * @param Entity\Voter $voter
     * @param Entity\EncryptedVote $encryptedVote
     */
    public function storeVote(Entity\Voter $voter, Entity\EncryptedVote $encryptedVote)
    {
        $storage = $this->getStorage();
        $storage->beginTransaction();
        
        try {
            $this->storeVoter($voter);
        } catch (\Exception $e) {
            $storage->rollback();
            throw $e;
        }
        
        try {
            $this->storeEncryptedVote($encryptedVote);
        } catch (\Exception $e) {
            $storage->rollback();
            throw $e;
        }
        
        $storage->commit();
    }


    /**
     * Saves the voter ID to the storage.
     * 
     * @param Entity\Voter $voter
     * @throws Exception\VoteStorageException
     */
    public function storeVoter(Entity\Voter $voter)
    {
        try {
            $this->getStorage()->saveVoterId($voter->getId());
        } catch (\Exception $e) {
            throw new Exception\VoteStorageException(
                $this->formatExceptionMessage($e, sprintf("Exception while storing voter ID '%s'", $voter->getId())), 
                null, $e);
        }
    }


    /**
     * Saves an encrypted vote to the storage.
     * 
     * @param Entity\EncryptedVote $encryptedVote
     * @throws Exception\VoteStorageException
     */
    public function storeEncryptedVote(Entity\EncryptedVote $encryptedVote)
    {
        try {
            $this->getStorage()->save($encryptedVote);
        } catch (\Exception $e) {
            throw new Exception\VoteStorageException(
                $this->formatExceptionMessage($e, 'Exception while storing encrypted vote'), null, $e);
        }
    }


    /**
     * Fetches all encrypted votes from the storage.
     * 
     * @throws Exception\VoteStorageException
     * @return \Elvo\Domain\Entity\Collection\EncryptedVoteCollection
     */
    public function fetchAllEncryptedVotes()
    {
        try {
            $encryptedVotes = $this->getStorage()->fetchAll();
        } catch (\Exception $e) {
            throw new Exception\VoteStorageException(
                $this->formatExceptionMessage($e, 'Exception while fetching encrypted votes'), null, $e);
        }
        
        return $encryptedVotes;
    }
    
    /*
     * 
     */
    
    /**
     * Formats a higher level exception message based on the lower level one.
     * 
     * @param \Exception $e
     * @param string $message
     * @return string
     */
    protected function formatExceptionMessage(\Exception $e, $message = 'Exception')
    {
        return sprintf("%s: [%s] %s", $message, get_class($e), $e->getMessage());
    }
}