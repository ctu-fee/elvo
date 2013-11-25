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
        
        $identity = new Identity($id, $roles);
        
        $this->assertSame($id, $identity->getId());
        $this->assertEquals($roles, $identity->getRoles());
    }
}