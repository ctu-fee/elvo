<?php

namespace ElvoTest\Domain\Entity;

use Elvo\Domain\Entity\Chamber;


class ChamberTest extends \PHPUnit_Framework_Testcase
{


    public function testConstructorWithInvalidCode()
    {
        $this->setExpectedException('Elvo\Domain\Entity\Exception\InvalidArgumentException');
        
        $code = 'invalid';
        
        $chamber = new Chamber($code);
    }


    public function testConstructorWithStudentCode()
    {
        $code = 'student';
        $chamber = new Chamber($code);
        $this->assertSame($code, (string) $chamber);
    }


    public function testConstructorWithAcademicCode()
    {
        $code = 'academic';
        $chamber = new Chamber($code);
        $this->assertSame($code, (string) $chamber);
    }
}