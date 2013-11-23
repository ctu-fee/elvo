<?php

namespace ElvoTest\Domain\Vote;

use Elvo\Util\Options;
use Elvo\Domain\Entity\Vote;
use Elvo\Domain\Entity\Collection\CandidateCollection;
use Elvo\Domain\Entity\VoterRole;
use Elvo\Domain\Vote\OpensslEncryptor;


class OpensslEncryptorTest extends \PHPUnit_Framework_Testcase
{


    public function testEncrypt()
    {
        $voterRole = VoterRole::academic();
        $candidates = new CandidateCollection();
        
        $vote = new Vote($voterRole, $candidates);
        
        $encryptor = new OpensslEncryptor(new Options(array(
            'private_key' => $this->getPrivateKeyPath(),
            'certificate' => $this->getCertificatePath()
        )));
        
        $encryptedVote = $encryptor->encryptVote($vote);
        $decryptedVote = $encryptor->decryptVote($encryptedVote);
    }
    
    /*
     * 
     */
    public function getCertificatePath()
    {
        return ELVO_TESTS_DATA_DIR . '/ssl/crypt.crt';
    }


    public function getPrivateKeyPath()
    {
        return ELVO_TESTS_DATA_DIR . '/ssl/crypt.key';
    }
}