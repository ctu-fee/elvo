<?php

namespace ElvoTest\Mvc\Authentication;

use Elvo\Mvc\Authentication\IdentityFactory;


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
        
        $this->factory->createIdentity(array(
            'foo' => 'bar'
        ));
    }


    public function testCreateIdentityWithEmptyUniqueId()
    {
        $this->setExpectedException('Elvo\Mvc\Authentication\Exception\MissingUniqueIdException');
        
        $this->factory->createIdentity(array(
            'voter_id' => ''
        ));
    }


    public function testCreateIdentity()
    {
        $id = 'abc';
        $roles = array(
            'foo',
            'bar'
        );
        
        $identity = $this->factory->createIdentity(array(
            'voter_id' => $id,
            'voter_roles' => $roles
        ));
        
        $this->assertInstanceOf('Elvo\Mvc\Authentication\Identity', $identity);
        $this->assertSame($id, $identity->getId());
        $this->assertEquals($roles, $identity->getRoles());
    }
}
