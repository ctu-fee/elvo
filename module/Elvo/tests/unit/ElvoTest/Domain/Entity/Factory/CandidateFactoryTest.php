<?php

namespace ElvoTest\Domain\Entity\Factory;

use Elvo\Domain\Entity\Chamber;
use Elvo\Domain\Entity\Factory\CandidateFactory;


class CandidateFactoryTest extends \PHPUnit_Framework_Testcase
{

    /**
     * @var CandidateFactory
     */
    protected $factory;


    public function setUp()
    {
        $this->factory = new CandidateFactory();
    }


    public function testSetHydrator()
    {
        $hydrator = $this->getMock('Zend\Stdlib\Hydrator\HydratorInterface');
        $this->factory->setHydrator($hydrator);
        $this->assertSame($hydrator, $this->factory->getHydrator());
    }


    public function testGetImplicitHydrator()
    {
        $hydrator = $this->factory->getHydrator();
        $this->assertInstanceOf('Zend\Stdlib\Hydrator\HydratorInterface', $this->factory->getHydrator());
    }


    public function testCreateCandidate()
    {
        $id = 123;
        $firstName = 'John';
        $lastName = 'Doe';
        $chamber = Chamber::academic();
        $email = 'john.doe@example.org';
        $profileUrl = 'http://profile';
        $candidateUrl = 'http://candidate';
        
        $data = array(
            'id' => $id,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'chamber' => $chamber,
            'email' => $email,
            'profile_url' => $profileUrl,
            'candidate_url' => $candidateUrl
        );
        
        $candidate = $this->factory->createCandidate($data);
        
        $this->assertInstanceOf('Elvo\Domain\Entity\Candidate', $candidate);
        $this->assertSame($id, $candidate->getId());
        $this->assertSame($firstName, $candidate->getFirstName());
        $this->assertSame($lastName, $candidate->getLastName());
        $this->assertSame($chamber, $candidate->getChamber());
        $this->assertSame($email, $candidate->getEmail());
        $this->assertSame($profileUrl, $candidate->getProfileUrl());
        $this->assertSame($candidateUrl, $candidate->getCandidateUrl());
    }
}