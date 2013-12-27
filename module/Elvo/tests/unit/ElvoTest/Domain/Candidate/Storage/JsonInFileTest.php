<?php

namespace ElvoTest\Domain\Candidate\Storage;

use Elvo\Domain\Entity\Chamber;
use Elvo\Domain\Candidate\Storage\JsonInFile;


class JsonInFileTest extends \PHPUnit_Framework_Testcase
{


    public function testFetchAllWithInvalidJson()
    {
        $this->setExpectedException('Elvo\Domain\Candidate\Storage\Exception\InvalidCandidateDataException');
        
        $storage = new JsonInFile(array(
            JsonInFile::OPT_FILE_PATH => ELVO_TESTS_DATA_DIR . '/candidates/candidates_invalid.json'
        ));
        
        $candidates = $storage->fetchAll();
    }


    public function testFetchAll()
    {
        $storage = new JsonInFile(array(
            JsonInFile::OPT_FILE_PATH => ELVO_TESTS_DATA_DIR . '/candidates/candidates.json'
        ));
        
        $candidates = $storage->fetchAll();
        
        $this->assertInstanceOf('Elvo\Domain\Entity\Collection\CandidateCollection', $candidates);
        
        $candidate = $candidates->offsetGet(0);
        $this->assertSame(123, $candidate->getId());
        $this->assertSame('Franta', $candidate->getFirstName());
        $this->assertSame('Vomacka', $candidate->getLastName());
        $this->assertEquals(Chamber::student(), $candidate->getChamber());
        $this->assertSame('vomacka@cvut.cz', $candidate->getEmail());
        $this->assertSame('http://candidate/url/vomacka', $candidate->getCandidateUrl());
        $this->assertSame('http://profile/url/vomacka', $candidate->getProfileUrl());
        
        $candidate = $candidates->offsetGet(1);
        $this->assertSame(456, $candidate->getId());
        $this->assertSame('Petr', $candidate->getFirstName());
        $this->assertSame('Novak', $candidate->getLastName());
        $this->assertEquals(Chamber::academic(), $candidate->getChamber());
        $this->assertSame('novak@cvut.cz', $candidate->getEmail());
        $this->assertSame('http://candidate/url/novak', $candidate->getCandidateUrl());
        $this->assertSame('http://profile/url/novak', $candidate->getProfileUrl());
    }
}