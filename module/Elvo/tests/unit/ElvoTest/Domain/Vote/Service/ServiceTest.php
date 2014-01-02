<?php

namespace ElvoTest\Domain\Vote\Service;

use Elvo\Domain\Vote\Service\Service;
use Elvo\Domain\Entity\Collection\EncryptedVoteCollection;
use Elvo\Domain\Vote\Service\Exception\VoteStorageException;


class ServiceTest extends \PHPUnit_Framework_TestCase
{

    protected $service;


    public function setUp()
    {
        $this->service = new Service($this->getManagerMock(), $this->getFactoryMock(), $this->getValidatorMock(), $this->getEncryptorMock(), $this->getStorageMock());
    }


    public function testConstructor()
    {
        $manager = $this->getManagerMock();
        $factory = $this->getFactoryMock();
        $validator = $this->getValidatorMock();
        $encryptor = $this->getEncryptorMock();
        $storage = $this->getStorageMock();
        
        $service = new Service($manager, $factory, $validator, $encryptor, $storage);
        
        $this->assertSame($manager, $service->getManager());
        $this->assertSame($factory, $service->getFactory());
        $this->assertSame($validator, $service->getValidator());
        $this->assertSame($encryptor, $service->getEncryptor());
        $this->assertSame($storage, $service->getStorage());
    }


    public function testIsVotingActive()
    {
        $result = true;
        
        $manager = $this->getManagerMock();
        $manager->expects($this->once())
            ->method('isVotingActive')
            ->will($this->returnValue($result));
        $this->service->setManager($manager);
        
        $this->assertSame($result, $this->service->isVotingActive());
    }


    public function testCheckVotingActive()
    {
        $this->setExpectedException('Elvo\Domain\Vote\Service\Exception\VotingInactiveException');
        
        $manager = $this->getManagerMock();
        $manager->expects($this->once())
            ->method('isVotingActive')
            ->will($this->returnValue(false));
        $this->service->setManager($manager);
        
        $this->service->checkVotingActive();
    }


    public function testHasAlreadyVoted()
    {
        $result = true;
        
        $voterId = '123';
        $voter = $this->getVoterMock($voterId);
        
        $storage = $this->getStorageMock();
        $storage->expects($this->once())
            ->method('existsVoterId')
            ->with($voterId)
            ->will($this->returnValue($result));
        $this->service->setStorage($storage);
        
        $this->assertSame($result, $this->service->hasAlreadyVoted($voter));
    }


    public function testHasAlreadyVotedById()
    {
        $result = true;
        
        $voterId = '123';
        
        $storage = $this->getStorageMock();
        $storage->expects($this->once())
            ->method('existsVoterId')
            ->with($voterId)
            ->will($this->returnValue($result));
        $this->service->setStorage($storage);
        
        $this->assertSame($result, $this->service->hasAlreadyVotedById($voterId));
    }


    public function testCheckHasAlreadyVoted()
    {
        $this->setExpectedException('Elvo\Domain\Vote\Service\Exception\VoterAlreadyVotedException');
        
        $voterId = '123';
        $voter = $this->getVoterMock($voterId);
        
        $storage = $this->getStorageMock();
        $storage->expects($this->once())
            ->method('existsVoterId')
            ->with($voterId)
            ->will($this->returnValue(true));
        $this->service->setStorage($storage);
        
        $this->service->checkHasAlreadyVoted($voter);
    }


    public function testCreateVoteWithException()
    {
        $this->setExpectedException('Elvo\Domain\Vote\Service\Exception\VoteCreationException');
        
        $voter = $this->getVoterMock();
        $candidates = $this->getCandidateCollectionMock();
        $exception = new \Exception('create_vote');
        
        $factory = $this->getFactoryMock();
        $factory->expects($this->once())
            ->method('createVote')
            ->with($voter, $candidates)
            ->will($this->throwException($exception));
        
        $this->service->setFactory($factory);
        $this->service->createVote($voter, $candidates);
    }


    public function testCreateVoteOk()
    {
        $voter = $this->getVoterMock();
        $candidates = $this->getCandidateCollectionMock();
        $vote = $this->getVoteMock();
        
        $factory = $this->getFactoryMock();
        $factory->expects($this->once())
            ->method('createVote')
            ->with($voter, $candidates)
            ->will($this->returnValue($vote));
        
        $this->service->setFactory($factory);
        $this->assertSame($vote, $this->service->createVote($voter, $candidates));
    }


    public function testValidateVoteWithException()
    {
        $this->setExpectedException('Elvo\Domain\Vote\Service\Exception\VoteValidationException');
        
        $vote = $this->getVoteMock();
        $exception = new \Exception('vote_validation');
        
        $validator = $this->getValidatorMock();
        $validator->expects($this->once())
            ->method('validate')
            ->with($vote)
            ->will($this->throwException($exception));
        $this->service->setValidator($validator);
        
        $this->service->validateVote($vote);
    }


    public function testValidateVoteOk()
    {
        $vote = $this->getVoteMock();
        
        $validator = $this->getValidatorMock();
        $validator->expects($this->once())
            ->method('validate')
            ->with($vote);
        $this->service->setValidator($validator);
        
        $this->service->validateVote($vote);
    }


    public function testEncryptVoteWithException()
    {
        $this->setExpectedException('Elvo\Domain\Vote\Service\Exception\VoteEncryptionException');
        
        $vote = $this->getVoteMock();
        $exception = new \Exception('vote_encryption');
        
        $encryptor = $this->getEncryptorMock();
        $encryptor->expects($this->once())
            ->method('encryptVote')
            ->with($vote)
            ->will($this->throwException($exception));
        $this->service->setEncryptor($encryptor);
        
        $this->service->encryptVote($vote);
    }


    public function testEncryptVoteOk()
    {
        $vote = $this->getVoteMock();
        $encryptedVote = $this->getEncryptedVoteMock();
        
        $encryptor = $this->getEncryptorMock();
        $encryptor->expects($this->once())
            ->method('encryptVote')
            ->with($vote)
            ->will($this->returnValue($encryptedVote));
        $this->service->setEncryptor($encryptor);
        
        $this->assertSame($encryptedVote, $this->service->encryptVote($vote));
    }


    public function testStoreVoterWithException()
    {
        $this->setExpectedException('Elvo\Domain\Vote\Service\Exception\VoteStorageException');
        
        $voterId = '123';
        $voter = $this->getVoterMock($voterId);
        $exception = new \Exception('vote_storage');
        
        $storage = $this->getStorageMock();
        $storage->expects($this->once())
            ->method('saveVoterId')
            ->with($voterId)
            ->will($this->throwException($exception));
        $this->service->setStorage($storage);
        
        $this->service->storeVoter($voter);
    }


    public function testStoreVoterOk()
    {
        $voterId = '123';
        $voter = $this->getVoterMock($voterId);
        $exception = new \Exception('vote_storage');
        
        $storage = $this->getStorageMock();
        $storage->expects($this->once())
            ->method('saveVoterId')
            ->with($voterId);
        
        $this->service->setStorage($storage);
        
        $this->service->storeVoter($voter);
    }


    public function testStoreEncryptedVoteWithException()
    {
        $this->setExpectedException('Elvo\Domain\Vote\Service\Exception\VoteStorageException');
        
        $encryptedVote = $this->getEncryptedVoteMock();
        $exception = new \Exception('vote_storage');
        
        $storage = $this->getStorageMock();
        $storage->expects($this->once())
            ->method('save')
            ->with($encryptedVote)
            ->will($this->throwException($exception));
        $this->service->setStorage($storage);
        
        $this->service->storeEncryptedVote($encryptedVote);
    }


    public function testStoreEncryptedVote()
    {
        $encryptedVote = $this->getEncryptedVoteMock();
        
        $storage = $this->getStorageMock();
        $storage->expects($this->once())
            ->method('save')
            ->with($encryptedVote);
        $this->service->setStorage($storage);
        
        $this->service->storeEncryptedVote($encryptedVote);
    }


    public function testSaveVote()
    {
        $voter = $this->getVoterMock();
        $candidates = $this->getCandidateCollectionMock();
        $vote = $this->getVoteMock();
        $encryptedVote = $this->getEncryptedVoteMock();
        
        $service = $this->getMockBuilder('Elvo\Domain\Vote\Service\Service')
            ->disableOriginalConstructor()
            ->setMethods(array(
            'checkVotingActive',
            'checkHasAlreadyVoted',
            'createVote',
            'validateVote',
            'encryptVote',
            'storeVote'
        ))
            ->getMock();
        
        $service->expects($this->once())
            ->method('checkVotingActive');
        
        $service->expects($this->once())
            ->method('checkHasAlreadyVoted')
            ->with($voter);
        
        $service->expects($this->once())
            ->method('createVote')
            ->with($voter, $candidates)
            ->will($this->returnValue($vote));
        
        $service->expects($this->once())
            ->method('validateVote')
            ->with($vote);
        
        $service->expects($this->once())
            ->method('encryptVote')
            ->with($vote)
            ->will($this->returnValue($encryptedVote));
        
        $service->expects($this->once())
            ->method('storeVote')
            ->with($voter, $encryptedVote);
        
        $service->saveVote($voter, $candidates);
    }


    public function testFetchAllEncryptedVotesWithException()
    {
        $this->setExpectedException('Elvo\Domain\Vote\Service\Exception\VoteStorageException');
        
        $exception = new \Exception('fetch_exception');
        
        $storage = $this->getStorageMock();
        $storage->expects($this->once())
            ->method('fetchAll')
            ->will($this->throwException($exception));
        $this->service->setStorage($storage);
        
        $this->service->fetchAllEncryptedVotes();
    }


    public function testFetchAllEncryptedVotesOk()
    {
        $encryptedVotes = $this->getEncryptedVoteCollectionMock();
        
        $storage = $this->getStorageMock();
        $storage->expects($this->once())
            ->method('fetchAll')
            ->will($this->returnValue($encryptedVotes));
        $this->service->setStorage($storage);
        
        $this->assertSame($encryptedVotes, $this->service->fetchAllEncryptedVotes());
    }


    public function testDecryptVoteWithException()
    {
        $this->setExpectedException('Elvo\Domain\Vote\Service\Exception\VoteEncryptionException');
        
        $encryptedVote = $this->getEncryptedVoteMock();
        $exception = new \Exception('decrypt_vote');
        
        $encryptor = $this->getEncryptorMock();
        $encryptor->expects($this->once())
            ->method('decryptVote')
            ->with($encryptedVote)
            ->will($this->throwException($exception));
        $this->service->setEncryptor($encryptor);
        
        $this->service->decryptVote($encryptedVote);
    }


    public function testDecryptVoteOk()
    {
        $encryptedVote = $this->getEncryptedVoteMock();
        $vote = $this->getVoteMock();
        
        $encryptor = $this->getEncryptorMock();
        $encryptor->expects($this->once())
            ->method('decryptVote')
            ->with($encryptedVote)
            ->will($this->returnValue($vote));
        $this->service->setEncryptor($encryptor);
        
        $this->assertSame($vote, $this->service->decryptVote($encryptedVote));
    }


    public function testFetchAllVotes()
    {
        $encVote1 = $this->getEncryptedVoteMock();
        $encVote2 = $this->getEncryptedVoteMock();
        
        $vote1 = $this->getVoteMock();
        $vote2 = $this->getVoteMock();
        
        $encryptedVoteCollection = new EncryptedVoteCollection();
        $encryptedVoteCollection->append($encVote1);
        $encryptedVoteCollection->append($encVote2);
        
        $service = $this->getMockBuilder('Elvo\Domain\Vote\Service\Service')
            ->disableOriginalConstructor()
            ->setMethods(array(
            'fetchAllEncryptedVotes',
            'decryptVote'
        ))
            ->getMock();
        
        $service->expects($this->once())
            ->method('fetchAllEncryptedVotes')
            ->will($this->returnValue($encryptedVoteCollection));
        
        $service->expects($this->at(1))
            ->method('decryptVote')
            ->with($encVote1)
            ->will($this->returnValue($vote1));
        
        $service->expects($this->at(2))
            ->method('decryptVote')
            ->with($encVote2)
            ->will($this->returnValue($vote2));
        
        $votes = $service->fetchAllVotes();
        $this->assertInstanceOf('Elvo\Domain\Entity\Collection\VoteCollection', $votes);
        $this->assertSame($vote1, $votes->offsetGet(0));
        $this->assertSame($vote2, $votes->offsetGet(1));
    }


    public function testCountVotes()
    {
        $count = 1010101;
        
        $storage = $this->getStorageMock();
        $storage->expects($this->once())
            ->method('count')
            ->will($this->returnValue($count));
        $this->service->setStorage($storage);
        
        $this->assertSame($count, $this->service->countVotes());
    }


    public function testStoreVoteWithStoreVoterException()
    {
        $this->setExpectedException('Elvo\Domain\Vote\Service\Exception\VoteStorageException', 'storage_error');
        
        $exception = new VoteStorageException('storage_error');
        $voter = $this->getVoterMock();
        $vote = $this->getEncryptedVoteMock();
        
        $service = $this->getMockBuilder('Elvo\Domain\Vote\Service\Service')
            ->disableOriginalConstructor()
            ->setMethods(array(
            'storeVoter'
        ))
            ->getMock();
        $service->expects($this->once())
            ->method('storeVoter')
            ->with($voter)
            ->will($this->throwException($exception));
        
        $storage = $this->getStorageMock();
        $storage->expects($this->once())
            ->method('beginTransaction');
        $storage->expects($this->once())
            ->method('rollback');
        
        $service->setStorage($storage);
        
        $service->storeVote($voter, $vote);
    }


    public function testStoreVoteWithStoreEncryptedVoteException()
    {
        $this->setExpectedException('Elvo\Domain\Vote\Service\Exception\VoteStorageException', 'storage_error');
        
        $exception = new VoteStorageException('storage_error');
        $voter = $this->getVoterMock();
        $vote = $this->getEncryptedVoteMock();
        
        $service = $this->getMockBuilder('Elvo\Domain\Vote\Service\Service')
            ->disableOriginalConstructor()
            ->setMethods(array(
            'storeEncryptedVote'
        ))
            ->getMock();
        $service->expects($this->once())
            ->method('storeEncryptedVote')
            ->with($vote)
            ->will($this->throwException($exception));
        
        $storage = $this->getStorageMock();
        $storage->expects($this->once())
            ->method('beginTransaction');
        $storage->expects($this->once())
            ->method('rollback');
        
        $service->setStorage($storage);
        
        $service->storeVote($voter, $vote);
    }


    public function testStoreVoteWithCommit()
    {
        $exception = new VoteStorageException('storage_error');
        $voter = $this->getVoterMock();
        $vote = $this->getEncryptedVoteMock();
        
        $service = $this->getMockBuilder('Elvo\Domain\Vote\Service\Service')
            ->disableOriginalConstructor()
            ->setMethods(array(
            'storeVoter',
            'storeEncryptedVote'
        ))
            ->getMock();
        
        $service->expects($this->once())
            ->method('storeVoter')
            ->with($voter);
        $service->expects($this->once())
            ->method('storeEncryptedVote')
            ->with($vote);
        
        $storage = $this->getStorageMock();
        $storage->expects($this->once())
            ->method('beginTransaction');
        $storage->expects($this->once())
            ->method('commit');
        
        $service->setStorage($storage);
        
        $service->storeVote($voter, $vote);
    }
    
    /*
     * 
     */
    protected function getManagerMock()
    {
        $manager = $this->getMock('Elvo\Domain\Vote\VoteManager');
        return $manager;
    }


    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getFactoryMock()
    {
        $factory = $this->getMock('Elvo\Domain\Entity\Factory\VoteFactoryInterface');
        return $factory;
    }


    protected function getValidatorMock()
    {
        $validator = $this->getMock('Elvo\Domain\Vote\Validator\ValidatorInterface');
        return $validator;
    }


    protected function getEncryptorMock()
    {
        $encryptor = $this->getMock('Elvo\Domain\Vote\EncryptorInterface');
        return $encryptor;
    }


    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getStorageMock()
    {
        $storage = $this->getMock('Elvo\Domain\Vote\Storage\StorageInterface');
        return $storage;
    }


    protected function getVoterMock($id = null)
    {
        $voter = $this->getMockBuilder('Elvo\Domain\Entity\Voter')
            ->disableOriginalConstructor()
            ->getMock();
        if ($id) {
            $voter->expects($this->any())
                ->method('getId')
                ->will($this->returnValue($id));
        }
        
        return $voter;
    }


    protected function getCandidateCollectionMock()
    {
        $candidates = $this->getMock('Elvo\Domain\Entity\Collection\CandidateCollection');
        return $candidates;
    }


    protected function getVoteMock()
    {
        $vote = $this->getMockBuilder('Elvo\Domain\Entity\Vote')
            ->disableOriginalConstructor()
            ->getMock();
        return $vote;
    }


    protected function getEncryptedVoteMock()
    {
        $encryptedVote = $this->getMockBuilder('Elvo\Domain\Entity\EncryptedVote')
            ->disableOriginalConstructor()
            ->getMock();
        return $encryptedVote;
    }


    protected function getEncryptedVoteCollectionMock()
    {
        $encryptedVotes = $this->getMock('Elvo\Domain\Entity\Collection\EncryptedVoteCollection');
        return $encryptedVotes;
    }
}