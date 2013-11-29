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


    /**
     * @dataProvider createIdentityProvider
     */
    public function testDecodeRoles($encodedRoles, $decodedRoles)
    {
        $this->assertEquals($decodedRoles, $this->factory->decodeRoles($encodedRoles));
    }


    public function testCreateIdentity()
    {
        $id = 'abc';
        $encodedRoles = 4;
        $expectedRoles = array(
            'academic'
        );
        
        $identity = $this->factory->createIdentity($this->getIdentityData(array(
            'voter_id' => $id,
            'voter_roles' => $encodedRoles
        )));
        
        $this->assertInstanceOf('Elvo\Mvc\Authentication\Identity', $identity);
        $this->assertSame($id, $identity->getId());
        $this->assertEquals($expectedRoles, $identity->getRoles());
    }


    public function createIdentityProvider()
    {
        return array(
            array(
                'encodedRoles' => - 1,
                'decodedRoles' => array()
            ),
            array(
                'encodedRoles' => 0,
                'decodedRoles' => array()
            ),
            array(
                'encodedRoles' => 1,
                'decodedRoles' => array(
                    'student'
                )
            ),
            array(
                'encodedRoles' => 2,
                'decodedRoles' => array(
                    'academic'
                )
            ),
            array(
                'encodedRoles' => 3,
                'decodedRoles' => array(
                    'student',
                    'academic'
                )
            ),
            array(
                'encodedRoles' => 4,
                'decodedRoles' => array(
                    'academic'
                )
            ),
            array(
                'encodedRoles' => 5,
                'decodedRoles' => array(
                    'student',
                    'academic'
                )
            ),
            array(
                'encodedRoles' => 6,
                'decodedRoles' => array(
                    'academic'
                )
            ),
            array(
                'encodedRoles' => 7,
                'decodedRoles' => array(
                    'student',
                    'academic'
                )
            )
        );
    }


    protected function getIdentityData($userData = array(), $systemData = array())
    {
        return new Data($userData, $systemData);
    }
}
