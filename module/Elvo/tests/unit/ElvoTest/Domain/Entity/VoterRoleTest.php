<?php

namespace ElvoTest\Domain\Entity;

use Elvo\Domain\Entity\VoterRole;


class VoterRoleTest extends \PHPUnit_Framework_Testcase
{


    public function testConstructorWithStudentRole()
    {
        $this->assertSame(VoterRole::STUDENT, (string) VoterRole::student());
    }


    public function testConstructorWithAcademicRole()
    {
        $this->assertSame(VoterRole::ACADEMIC, (string) VoterRole::academic());
    }


    public function testConstructorWithInvalidRole()
    {
        $this->setExpectedException('Elvo\Domain\Entity\Exception\InvalidVoterRoleException');
        $role = new VoterRole('foo');
    }
}