<?php

namespace ElvoFuncTest;

use Elvo\Mvc\Candidate\CandidateService;
use Elvo\Domain\Entity\Factory\CandidateFactory;
use Elvo\Domain\Entity\Chamber;


class CandidateServiceTest extends \PHPUnit_Framework_TestCase
{


    public function testSetCandidatesWithInvalidArgument()
    {
        $this->setExpectedException('Elvo\Mvc\Candidate\Exception\InvalidCandidateDataException', 'Expected CandidateCollection or array');
        
        $candidates = 'scalar';
        $candidateFactory = new CandidateFactory();
        $service = new CandidateService($candidateFactory, $candidates);
    }


    public function testSetCandidatesWithInvalidArray()
    {
        $this->setExpectedException('Elvo\Mvc\Candidate\Exception\InvalidCandidateDataException', 'Each candidate item must have the "id" property set');
        
        $candidates = array(
            array(
                'foo' => 'bar'
            )
        );
        $candidateFactory = new CandidateFactory();
        $service = new CandidateService($candidateFactory, $candidates);
    }


    public function testSetCandidatesWithInvalidChamber()
    {
        $this->setExpectedException('Elvo\Domain\Entity\Exception\InvalidChamberCodeException');
        
        $candidates = array(
            array(
                'id' => 1,
                'chamber' => 'foo'
            ),
            array(
                'id' => 2,
                'chamber' => 'bar'
            )
        );
        $candidateFactory = new CandidateFactory();
        $service = new CandidateService($candidateFactory, $candidates);
    }


    public function testSetCandidates()
    {
        $candidates = array(
            array(
                'id' => 1,
                'chamber' => 'student'
            ),
            array(
                'id' => 2,
                'chamber' => 'academic'
            )
        );
        $candidateFactory = new CandidateFactory();
        $service = new CandidateService($candidateFactory, $candidates);
        
        $candidates = $service->getCandidates();
        
        $this->assertInstanceOf('Elvo\Domain\Entity\Collection\CandidateCollection', $candidates);
        $this->assertCount(2, $candidates);
    }


    public function testGetCandidatesForChamber()
    {
        $candidates = array(
            array(
                'id' => 1,
                'chamber' => 'student'
            ),
            array(
                'id' => 2,
                'chamber' => 'academic'
            ),
            array(
                'id' => 3,
                'chamber' => 'student'
            ),
            array(
                'id' => 4,
                'chamber' => 'academic'
            ),
            array(
                'id' => 2,
                'chamber' => 'academic'
            )
        );
        $candidateFactory = new CandidateFactory();
        $service = new CandidateService($candidateFactory, $candidates);
        
        $studentCandidates = $service->getCandidatesForChamber(Chamber::student());
        $this->assertInstanceOf('Elvo\Domain\Entity\Collection\CandidateCollection', $studentCandidates);
        $this->assertCount(2, $studentCandidates);
        $this->assertSame(1, $studentCandidates->offsetGet(0)
            ->getId());
        $this->assertSame(3, $studentCandidates->offsetGet(1)
            ->getId());
    }


    public function testLoadCandidatesFromFile()
    {
        $file = ELVO_TESTS_DATA_DIR . '/candidates.php';
        
        $candidateFactory = new CandidateFactory();
        $service = new CandidateService($candidateFactory, $file);
        $candidates = $service->getCandidates();
        
        $this->assertInstanceOf('Elvo\Domain\Entity\Collection\CandidateCollection', $candidates);
        $this->assertCount(1, $candidates);
        
        $candidate = $candidates->offsetGet(0);
        $this->assertSame(123, $candidate->getId());
        $this->assertSame('Franta', $candidate->getFirstName());
        $this->assertSame('Vomacka', $candidate->getLastName());
        $this->assertSame('student', $candidate->getChamber()
            ->getCode());
    }
}
