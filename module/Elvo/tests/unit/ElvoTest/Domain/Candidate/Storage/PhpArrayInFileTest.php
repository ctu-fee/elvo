<?php

namespace ElvoTest\Domain\Candidate\Storage;

use Elvo\Domain\Candidate\Storage\PhpArrayInFile;
use Elvo\Util\Options;


class PhpArrayInFileTest extends \PHPUnit_Framework_TestCase
{

    protected $storage;


    public function setUp()
    {
        $this->storage = new PhpArrayInFile(new Options());
        
        $file = ELVO_TESTS_DATA_DIR . '/candidates/candidates_unreadable.php';
        chmod($file, 0000);
    }


    public function tearDown()
    {
        $file = ELVO_TESTS_DATA_DIR . '/candidates/candidates_unreadable.php';
        chmod($file, 0644);
    }


    public function testFetchAllWithMissingOption()
    {
        $this->setExpectedException('InGeneral\Exception\MissingOptionException', "Missing option 'file_path'");
        
        $this->storage->fetchAll();
    }


    public function testFetchAllWithInvalidFile()
    {
        $file = __DIR__;
        $this->setExpectedException('Elvo\Domain\Candidate\Storage\Exception\InvalidFileException', sprintf("Invalid file '%s'", $file));
        $this->storage->setOptions(array(
            'file_path' => $file
        ));
        $this->storage->fetchAll();
    }


    public function testFetchAllWithUnreadableFile()
    {
        $file = ELVO_TESTS_DATA_DIR . '/candidates/candidates_unreadable.php';
        $this->setExpectedException('Elvo\Domain\Candidate\Storage\Exception\InvalidFileException', sprintf("Cannot read file '%s'", $file));
        $this->storage->setOptions(array(
            'file_path' => $file
        ));
        $this->storage->fetchAll();
    }


    public function testFetchAllWithInvalidData()
    {
        $file = ELVO_TESTS_DATA_DIR . '/candidates/candidates_invalid.php';
        $this->setExpectedException('Elvo\Domain\Candidate\Service\Exception\InvalidCandidateDataException', sprintf("Invalid candidate data in file '%s'", $file));
        $this->storage->setOptions(array(
            'file_path' => $file
        ));
        $this->storage->fetchAll();
    }


    public function testFetchAllWithFactoryException()
    {
        $this->setExpectedException('Elvo\Domain\Candidate\Storage\Exception\InvalidCandidateDataException');
        
        $file = ELVO_TESTS_DATA_DIR . '/candidates/candidates.php';
        $this->storage->setOptions(array(
            'file_path' => $file
        ));
        $exception = new \Exception();
        
        $candidatesData = require $file;
        
        $factory = $this->createCandidateFactoryMock();
        $factory->expects($this->once())
            ->method('createCandidate')
            ->with($candidatesData[0])
            ->will($this->throwException($exception));
        $this->storage->setCandidateFactory($factory);
        
        $this->storage->fetchAll();
    }


    public function testFetchAll()
    {
        $file = ELVO_TESTS_DATA_DIR . '/candidates/candidates.php';
        $this->storage->setOptions(array(
            'file_path' => $file
        ));
        
        $candidatesData = require $file;
  
        $candidate1 = $this->createCandidateMock();
        $candidate2 = $this->createCandidateMock();
        
        $factory = $this->createCandidateFactoryMock();
        $factory->expects($this->at(0))
            ->method('createCandidate')
            ->with($candidatesData[0])
            ->will($this->returnValue($candidate1));
        $factory->expects($this->at(1))
            ->method('createCandidate')
            ->with($candidatesData[1])
            ->will($this->returnValue($candidate2));
        $this->storage->setCandidateFactory($factory);
        
        $candidateCollection = $this->storage->fetchAll();
        
        $this->assertInstanceOf('Elvo\Domain\Entity\Collection\CandidateCollection', $candidateCollection);
        $this->assertSame($candidate1, $candidateCollection->offsetGet(0));
        $this->assertSame($candidate2, $candidateCollection->offsetGet(1));
    }
    
    /*
     * 
     */
    protected function createCandidateFactoryMock()
    {
        $factory = $this->getMock('Elvo\Domain\Entity\Factory\CandidateFactory');
        return $factory;
    }


    protected function createCandidateMock()
    {
        $candidate = $this->getMock('Elvo\Domain\Entity\Candidate');
        return $candidate;
    }
}