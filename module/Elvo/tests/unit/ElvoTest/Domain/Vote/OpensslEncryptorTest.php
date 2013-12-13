<?php

namespace ElvoTest\Domain\Vote;

use Elvo\Util\Options;
use Elvo\Domain\Vote\OpensslEncryptor;


class OpensslEncryptorTest extends \PHPUnit_Framework_Testcase
{


    public function testEncrypVoteWithMissingCertificate()
    {
        $this->setExpectedException('Elvo\Util\Exception\MissingOptionException', "Missing option 'certificate'");
        
        $vote = $this->getVoteMock();
        
        $encryptor = new OpensslEncryptor(new Options());
        $encryptor->encryptVote($vote);
    }


    public function testEncryptVote()
    {
        $vote = $this->getVoteMock();
        
        $certificate = $this->getCertificatePath();
        $serializedVote = 'blahblah';
        $encData = 'blehbleh';
        $envelopeKey = 'key';
        $envelopeKeys = array(
            $envelopeKey
        );
        
        $encryptor = new OpensslEncryptor(new Options(array(
            'certificate' => $certificate
        )));
        
        $serializer = $this->getSerializerMock();
        $serializer->expects($this->once())
            ->method('serialize')
            ->with($vote)
            ->will($this->returnValue($serializedVote));
        $encryptor->setSerializer($serializer);
        
        $algorithm = $this->getAlgorithmMock();
        $algorithm->expects($this->once())
            ->method('setPublicKey')
            ->with($certificate);
        $algorithm->expects($this->once())
            ->method('encrypt')
            ->with($serializedVote)
            ->will($this->returnValue($encData));
        $algorithm->expects($this->once())
            ->method('getEnvelopeKey')
            ->will($this->returnValue($envelopeKey));
        $encryptor->setAlgorithm($algorithm);
        
        $encryptedVote = $encryptor->encryptVote($vote);
        
        $this->assertInstanceOf('Elvo\Domain\Entity\EncryptedVote', $encryptedVote);
    }


    public function testDecryptVoteWithMissingPrivateKey()
    {
        $this->setExpectedException('Elvo\Util\Exception\MissingOptionException', "Missing option 'private_key'");
        
        $encVote = $this->getEncryptedVoteMock();
        
        $encryptor = new OpensslEncryptor(new Options());
        $encryptor->decryptVote($encVote);
    }


    public function testDecryptVote()
    {
        $privateKey = $this->getPrivateKeyPath();
        $envelopeKey = 'blahblah';
        $encryptedData = 'encrypted data';
        $serializedData = 'serialized data';
        $vote = $this->getVoteMock();
        $encVote = $this->getEncryptedVoteMock($encryptedData, $envelopeKey);
        
        $encryptor = new OpensslEncryptor(new Options(array(
            'private_key' => $privateKey
        )));
        
        $algorithm = $this->getAlgorithmMock();
        $algorithm->expects($this->once())
            ->method('setPrivateKey')
            ->with($privateKey);
        $algorithm->expects($this->once())
            ->method('setEnvelopeKey')
            ->with($envelopeKey);
        $algorithm->expects($this->once())
            ->method('decrypt')
            ->with($encryptedData)
            ->will($this->returnValue($serializedData));
        $encryptor->setAlgorithm($algorithm);
        
        $serializer = $this->getSerializerMock();
        $serializer->expects($this->once())
            ->method('unserialize')
            ->with($serializedData)
            ->will($this->returnValue($vote));
        $encryptor->setSerializer($serializer);
        
        $this->assertSame($vote, $encryptor->decryptVote($encVote));
    }
    
    /*
     * 
     */
    
    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function getSerializerMock()
    {
        $serializer = $this->getMock('Zend\Serializer\Adapter\AdapterInterface');
        return $serializer;
    }


    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function getAlgorithmMock()
    {
        $algorithm = $this->getMock('Elvo\Domain\Vote\OpensslAlgorithmInterface');
        return $algorithm;
    }


    public function getVoteMock()
    {
        $vote = $this->getMockBuilder('Elvo\Domain\Entity\Vote')
            ->disableOriginalConstructor()
            ->getMock();
        return $vote;
    }


    public function getEncryptedVoteMock($data = null, $key = null)
    {
        $encVote = $this->getMockBuilder('Elvo\Domain\Entity\EncryptedVote')
            ->disableOriginalConstructor()
            ->getMock();
        if ($data) {
            $encVote->expects($this->once())
                ->method('getData')
                ->will($this->returnValue($data));
        }
        if ($key) {
            $encVote->expects($this->once())
                ->method('getKey')
                ->will($this->returnValue($key));
        }
        return $encVote;
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