<?php

namespace ElvoTest\Domain\Vote\Validator;


class AbstractValidatorTest extends \PHPUnit_Framework_Testcase
{


    public function testConstructorWithImplicitOptions()
    {
        $validator = $this->getMockBuilder('Elvo\Domain\Vote\Validator\AbstractValidator')->getMockForAbstractClass();
        $this->assertInstanceOf('Elvo\Util\Options', $validator->getOptions());
    }


    public function testConstructorWithExplicitOptions()
    {
        $options = $this->getMock('Elvo\Util\Options');
        $validator = $this->getMockBuilder('Elvo\Domain\Vote\Validator\AbstractValidator')
            ->setConstructorArgs(array(
            $options
        ))
            ->getMockForAbstractClass();
        
        $this->assertSame($options, $validator->getOptions());
    }
}