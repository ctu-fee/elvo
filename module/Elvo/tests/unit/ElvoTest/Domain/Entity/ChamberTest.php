<?php

namespace ElvoTest\Domain\Entity;

use Elvo\Domain\Entity\Chamber;


class ChamberTest extends \PHPUnit_Framework_Testcase
{


    public function testConstructStudent()
    {
        $this->assertSame(Chamber::STUDENT, (string) Chamber::student());
    }


    public function testConstructAcademic()
    {
        $this->assertSame(Chamber::ACADEMIC, (string) Chamber::academic());
    }


    public function testConstructorWithInvalidChamber()
    {
        $this->setExpectedException('Elvo\Domain\Entity\Exception\InvalidChamberCodeException');
        $chamber = new Chamber('foo');
    }
}