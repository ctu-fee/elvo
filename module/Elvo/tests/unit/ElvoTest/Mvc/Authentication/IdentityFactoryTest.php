<?php

namespace ElvoTest\Mvc\Authentication;

use Elvo\Mvc\Authentication\IdentityFactory;
use ZfcShib\Authentication\Identity\Data;


class IdentityFactoryTest extends \PHPUnit_Framework_TestCase
{

    protected $factory;


    public function setUp()
    {
        $this->factory = new IdentityFactory();
    }


    public function testCreateIdentityWithoutUniqueId()
    {
        $this->setExpectedException('Elvo\Mvc\Authentication\Exception\MissingUniqueIdException');
        
        $this->factory->createIdentity($this->getIdentityData(array(
            'foo' => 'bar'
        )));
    }


    public function testCreateIdentityWithEmptyUniqueId()
    {
        $this->setExpectedException('Elvo\Mvc\Authentication\Exception\MissingUniqueIdException');
        
        $this->factory->createIdentity($this->getIdentityData(array(
            'voter_id' => ''
        )));
    }


    public function testCreateIdentityWithMissingRole()
    {
        $this->setExpectedException('Elvo\Mvc\Authentication\Exception\MissingRoleException');
        
        $this->factory->createIdentity($this->getIdentityData(array(
            'voter_id' => '123'
        )));
    }


    public function testCreateIdentity()
    {
        $id = 'abc';
        $encodedRoles = 4;
        $expectedRoles = array(
            'academic'
        );
        
        $extractor = $this->getMock('Elvo\Mvc\Authentication\Role\RoleExtractorInterface');
        $extractor->expects($this->once())
            ->method('extractRoles')
            ->with($encodedRoles)
            ->will($this->returnValue($expectedRoles));
        $this->factory->setRoleExtractor($extractor);
        
        $identity = $this->factory->createIdentity($this->getIdentityData(array(
            'voter_id' => $id,
            'voter_roles' => $encodedRoles
        )));
        
        $this->assertInstanceOf('Elvo\Mvc\Authentication\Identity', $identity);
        $this->assertSame($id, $identity->getId());
        $this->assertEquals($expectedRoles, $identity->getRoles());
    }


    protected function getIdentityData($userData = array(), $systemData = array())
    {
        return new Data($userData, $systemData);
    }
}
