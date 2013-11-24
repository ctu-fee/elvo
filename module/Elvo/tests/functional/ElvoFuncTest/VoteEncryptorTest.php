<?php

namespace ElvoFuncTest;

use Elvo\Util\Options;
use Elvo\Domain\Vote\OpensslEncryptor;
use Elvo\Domain\Entity\Factory\CandidateFactory;
use Elvo\Domain\Entity\Candidate;
use Elvo\Domain\Entity\Collection\CandidateCollection;
use Elvo\Domain\Entity\VoterRole;
use Elvo\Domain\Entity\Vote;


class VoteEncryptorTest extends \PHPUnit_Framework_Testcase
{


    public function testEncryptVote()
    {
        $voterRole = VoterRole::academic();
        $candidateId = 123;
        
        $candidateFactory = new CandidateFactory();
        $candidate = $candidateFactory->createCandidate(array(
            'id' => $candidateId
        ));
        
        $candidates = new CandidateCollection();
        $candidates->append($candidate);
        
        $vote = new Vote($voterRole, $candidates);
        
        $encryptor = new OpensslEncryptor(new Options(array(
            'certificate' => $this->getCertificatePath(),
            'private_key' => $this->getPrivateKeyPath()
        )));
        
        $encryptedVote = $encryptor->encryptVote($vote);
        $decryptedVote = $encryptor->decryptVote($encryptedVote);
        
        $this->assertSame((string) $voterRole, (string) $decryptedVote->getVoterRole());
        
        $expectedCandidates = $decryptedVote->getCandidates();
        $this->assertSame(1, $expectedCandidates->count());
        $candidatesArray = $expectedCandidates->getArrayCopy();
        
        $this->assertSame($candidateId, $candidatesArray[0]->getId());
    }


    public function getCertificatePath()
    {
        return ELVO_TESTS_DATA_DIR . '/ssl/crypt.crt';
    }


    public function getPrivateKeyPath()
    {
        return ELVO_TESTS_DATA_DIR . '/ssl/crypt.key';
    }
}