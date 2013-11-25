<?php

namespace ElvoTest\Mvc\Authentication;

use Elvo\Mvc\Authentication\Identity;


class IdentityTest extends \PHPUnit_Framework_TestCase
{


    public function testConstructor()
    {
        $id = 'qwerty';
        $roles = array(
            'foo',
            'bar'
        );
        $validRoles = array(
            'foo',
            'bar',
            'other'
        );
        
        $identity = $this->createIdentity($id, $roles, $validRoles);
        
        $this->assertSame($id, $identity->getId());
        $this->assertEquals($roles, $identity->getRoles());
    }


    public function testHasMultipleRolesFalse()
    {
        $identity = $this->createIdentity('abc', array(
            'student'
        ));
        $this->assertFalse($identity->hasMultipleRoles());
    }


    public function testHasMultipleRolesTrue()
    {
        $identity = $this->createIdentity('abc', array(
            'student',
            'academic'
        ));
        $this->assertTrue($identity->hasMultipleRoles());
    }


    public function testHasRoleFalse()
    {
        $identity = $this->createIdentity('abc', array(
            'student'
        ));
        $this->assertFalse($identity->hasRole('bar'));
    }


    public function testHasRoleTrue()
    {
        $identity = $this->createIdentity('abc', array(
            'student',
            'academic'
        ));
        $this->assertTrue($identity->hasRole('student'));
    }


    public function testIsValidRole()
    {
        $validRoles = array(
            'foo',
            'bar'
        );
        $identity = $this->createIdentity('abc', array(
            'foo',
            'bar'
        ), array(
            'foo',
            'bar'
        ));
        
        $this->assertTrue($identity->isValidRole('foo'));
        $this->assertTrue($identity->isValidRole('bar'));
        $this->assertFalse($identity->isValidRole('other'));
    }
    
    /*
     * 
     */
    protected function createIdentity($id, array $roles = array(), $validRoles = null)
    {
        return new Identity($id, $roles, $validRoles);
    }
}