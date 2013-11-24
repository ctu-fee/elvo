<?php

namespace ElvoTest\Domain\Vote\Validator;

use Elvo\Domain\Vote\Validator\ChainValidator;


class ChainValidatorTest extends \PHPUnit_Framework_Testcase
{


    public function testValidate()
    {
        $vote = $this->getVoteMock();
        
        $validators = array(
            $this->getValidatorMock($vote),
            $this->getValidatorMock($vote),
            $this->getValidatorMock($vote)
        );
        
        $validator = new ChainValidator($validators);
        $validator->validate($vote);
    }


    protected function getVoteMock()
    {
        $vote = $this->getMockBuilder('Elvo\Domain\Entity\Vote')
            ->disableOriginalConstructor()
            ->getMock();
        return $vote;
    }


    protected function getValidatorMock($vote)
    {
        $validator = $this->getMock('Elvo\Domain\Vote\Validator\ValidatorInterface');
        $validator->expects($this->once())
            ->method('validate')
            ->with($vote);
        
        return $validator;
    }
}