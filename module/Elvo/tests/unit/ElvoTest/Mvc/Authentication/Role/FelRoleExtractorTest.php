<?php

namespace ElvoTest\Mvc\Authentication\Role;

use Elvo\Mvc\Authentication\Role\FelRoleExtractor;


class FelRoleExtractorTest extends \PHPUnit_Framework_Testcase
{

    protected $extractor;


    public function setUp()
    {
        $this->extractor = new FelRoleExtractor();
    }


    /**
     * @dataProvider rolesProvider
     */
    public function testExtractRoles($roleData, $roles)
    {
        $this->assertEquals($roles, $this->extractor->extractRoles($roleData));
    }


    public function rolesProvider()
    {
        return array(
            array(
                'roleData' => - 1,
                'roles' => array()
            ),
            array(
                'roleData' => 0,
                'roles' => array()
            ),
            array(
                'roleData' => 1,
                'roles' => array(
                    'student'
                )
            ),
            array(
                'roleData' => 2,
                'roles' => array(
                    'academic'
                )
            ),
            array(
                'roleData' => 3,
                'roles' => array(
                    'student',
                    'academic'
                )
            ),
            array(
                'roleData' => 4,
                'roles' => array(
                    'academic'
                )
            ),
            array(
                'roleData' => 5,
                'roles' => array(
                    'student',
                    'academic'
                )
            ),
            array(
                'roleData' => 6,
                'roles' => array(
                    'academic'
                )
            ),
            array(
                'roleData' => 7,
                'roles' => array(
                    'student',
                    'academic'
                )
            )
        );
    }
}