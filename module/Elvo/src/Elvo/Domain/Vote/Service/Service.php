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
    public function __construct(VoteFactoryInterface $factory, Vote\Validator\ValidatorInterface $validator, Vote\EncryptorInterface $encryptor, Vote\Storage\StorageInterface $storage)
    {
        $this->setFactory($factory);
        $this->setValidator($validator);
        $this->setEncryptor($encryptor);
        $this->setStorage($storage);
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
     * @see \Elvo\Domain\Vote\Service\ServiceInterface::saveVote()
     */
    public function saveVote(Entity\Voter $voter, CandidateCollection $candidates)
    {
        $vote = $this->createVote($voter, $candidates);
        $this->validateVote($vote);
        
        $encryptedVote = $this->encryptVote($vote);
        $this->storeEncryptedVote($encryptedVote);
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
            throw new Exception\VoteValidationException($this->formatExceptionMessage($e, 'Vote validation exception'), null, $e);
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
            throw new Exception\VoteEncryptionException($this->formatExceptionMessage($e, 'Exception during vote encryption'), null, $e);
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
            throw new Exception\VoteEncryptionException($this->formatExceptionMessage($e, 'Exception during vote decryption'), null, $e);
        }
        
        return $vote;
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
            throw new Exception\VoteStorageException($this->formatExceptionMessage($e, 'Exception while storing encrypted vote'), null, $e);
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
            throw new Exception\VoteStorageException($this->formatExceptionMessage($e, 'Exception while fetching encrypted votes'), null, $e);
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