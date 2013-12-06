<?php

namespace ElvoFuncTest;

use Elvo\Domain\Candidate\Service\Service;
use Elvo\Domain\Entity\Chamber;
use Elvo\Domain\Vote\VoteManager;
use Elvo\Domain\Candidate\Storage\PhpArrayInFile;


class CandidateServiceTest extends \PHPUnit_Framework_TestCase
{


    public function testGetCandidatesForChamberAcademic()
    {
        $service = $this->createCandidateService();
        
        $studentCandidates = $service->getCandidatesForChamber(Chamber::academic());
        $this->assertInstanceOf('Elvo\Domain\Entity\Collection\CandidateCollection', $studentCandidates);
        $this->assertCount(4, $studentCandidates);
        $this->assertSame(1, $studentCandidates->offsetGet(0)
            ->getId());
        $this->assertSame(2, $studentCandidates->offsetGet(1)
            ->getId());
        $this->assertSame(3, $studentCandidates->offsetGet(2)
            ->getId());
        $this->assertSame(4, $studentCandidates->offsetGet(3)
            ->getId());
    }


    public function testGetCandidatesForChamberStudent()
    {
        $service = $this->createCandidateService();
        
        $studentCandidates = $service->getCandidatesForChamber(Chamber::student());
        $this->assertInstanceOf('Elvo\Domain\Entity\Collection\CandidateCollection', $studentCandidates);
        $this->assertCount(3, $studentCandidates);
        $this->assertSame(5, $studentCandidates->offsetGet(0)
            ->getId());
        $this->assertSame(6, $studentCandidates->offsetGet(1)
            ->getId());
        $this->assertSame(7, $studentCandidates->offsetGet(2)
            ->getId());
    }
    
    /*
     * 
     */
    protected function createCandidateService()
    {
        $storage = new PhpArrayInFile(array(
            'file_path' => ELVO_TESTS_DATA_DIR . '/candidates/candidates_multiple.php'
        ));
        $voteManager = $this->createVoteManager();
        $service = new Service($storage, $voteManager);
        
        return $service;
    }


    protected function createVoteManager()
    {
        $voteManager = new VoteManager();
        return $voteManager;
    }
}
