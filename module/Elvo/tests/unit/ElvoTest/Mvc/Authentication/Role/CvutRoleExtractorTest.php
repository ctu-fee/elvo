<?php

namespace ElvoTest\Mvc\Authentication\Role;

use Elvo\Mvc\Authentication\Role\CvutRoleExtractor;
use Elvo\Util\Options;


class CvutRoleExtractorTest extends \PHPUnit_Framework_Testcase
{

    protected $extractor;


    public function setUp()
    {
        $this->extractor = new CvutRoleExtractor();
    }


    public function testRoleDataToArray()
    {
        $item1 = 'item1';
        $item2 = 'item2';
        $item3 = 'item3';
        
        $roleData = "$item1;$item2;$item3";
        $expectedData = array(
            $item1,
            $item2,
            $item3
        );
        
        $this->assertEquals($expectedData, $this->extractor->roleDataToArray($roleData));
    }


    public function testExtractRoleCodeFromComplexValueWithInvalidData()
    {
        $this->setExpectedException('Elvo\Mvc\Authentication\Role\Exception\InvalidRoleDataException');
        
        $this->extractor->extractRoleCodeFromComplexValue(array(
            'test'
        ));
    }


    /**
     * @dataProvider roleCodes
     */
    public function testParseRoleCode($roleCode, $expectedResult, $exceptionName)
    {
        if (null !== $exceptionName) {
            $this->setExpectedException($exceptionName);
        }
        
        $this->assertEquals($expectedResult, $this->extractor->parseRoleCode($roleCode));
    }


    /**
     * @dataProvider roleComplexValues
     */
    public function testExtractRoleCodeFromComplexValue($complex, $extracted)
    {
        $this->assertSame($extracted, $this->extractor->extractRoleCodeFromComplexValue($complex));
    }


    /**
     * @dataProvider roleDataToParse
     */
    public function testParseRoleData($roleData, $expectedRoles, $exceptionName = null)
    {
        if (null !== $exceptionName) {
            $this->setExpectedException($exceptionName);
        }
        
        $this->assertEquals($expectedRoles, $this->extractor->parseRoleData($roleData));
    }


    public function testFilterRolesByDepartmentCode()
    {
        $roles = array(
            array(
                'department_code' => '11000',
                'role_name' => 'role1'
            ),
            array(
                'department_code' => '12000',
                'role_name' => 'role2'
            ),
            array(
                'department_code' => '13000',
                'role_name' => 'role3'
            ),
            array(
                'department_code' => '11000',
                'role_name' => 'role5'
            ),
            array(
                'department_code' => '13000',
                'role_name' => 'role6'
            )
        );
        
        $expectedRoles1 = array(
            array(
                'department_code' => '13000',
                'role_name' => 'role3'
            ),
            array(
                'department_code' => '13000',
                'role_name' => 'role6'
            )
        );
        
        $expectedRoles2 = array(
            array(
                'department_code' => '11000',
                'role_name' => 'role1'
            ),
            array(
                'department_code' => '12000',
                'role_name' => 'role2'
            ),
            array(
                'department_code' => '11000',
                'role_name' => 'role5'
            )
        );
        
        $this->assertEquals($expectedRoles1, $this->extractor->filterRolesByDepartmentCode($roles, array(
            '13000'
        )));
        
        $this->assertEquals($expectedRoles2, $this->extractor->filterRolesByDepartmentCode($roles, array(
            '12000',
            '11000'
        )));
    }


    /**
     * @dataProvider roleData
     */
    public function testExtractRoles($roleData, $departmentCode, $expectedRoles)
    {
        $this->extractor->setOptions(new Options(array(
            'department_code' => $departmentCode
        )));
        
        $this->assertEquals($expectedRoles, $this->extractor->extractRoles($roleData));
    }
    
    /*
     * 
     */
    public function roleComplexValues()
    {
        return array(
            array(
                'complex' => '',
                'extracted' => null
            ),
            array(
                'complex' => 'foo',
                'extracted' => 'foo'
            ),
            array(
                'complex' => 'foo:bar',
                'extracted' => 'foo'
            )
        );
    }


    public function roleCodes()
    {
        return array(
            array(
                'code' => 'B-00000-FOO',
                'result' => array(
                    'department_code' => '000000',
                    'role_name' => 'FOO'
                ),
                'exceptionName' => null
            ),
            array(
                'code' => 'B-12345-FOO-BAR',
                'result' => array(
                    'department_code' => '012345',
                    'role_name' => 'FOO-BAR'
                ),
                'exceptionName' => null
            ),
            array(
                'code' => 'B-000000-FOO',
                'result' => null,
                'exceptionName' => '\Elvo\Mvc\Authentication\Role\Exception\InvalidRoleDataException'
            )
        );
    }


    public function roleDataToParse()
    {
        return array(
            array(
                'role_data' => 'B-13000-ZAMESTNANEC-NEAKADEMICKY:13000 - FEL - Fakulta elektrotechnická - neakademický zaměstnanec:13000:76:13000 - FEE - Faculty of Electrical Engineering - Non-academic employee;B-00000-ZAMESTNANEC-NEAKADEMICKY:00000 - ČVUT - České vysoké učení technické v Praze - neakademický zaměstnanec:00000:76:00000 - CTU - Czech Technical University in Prague - Non-academic employee',
                'expected_role' => array(
                    array(
                        'department_code' => '13000',
                        'role_name' => 'ZAMESTNANEC-NEAKADEMICKY'
                    ),
                    array(
                        'department_code' => '00000',
                        'role_name' => 'ZAMESTNANEC-NEAKADEMICKY'
                    )
                ),
                'exception' => null
            ),
            array(
                'role_data' => array(
                    'test'
                ),
                'expected_role' => null,
                'exception' => '\Elvo\Mvc\Authentication\Role\Exception\InvalidRoleDataException'
            ),
            array(
                'role_data' => array(
                    array(
                        'test'
                    )
                ),
                'expected_role' => null,
                'exception' => '\Elvo\Mvc\Authentication\Role\Exception\InvalidRoleDataException'
            ),
            array(
                'role_data' => 'foo;bar;test',
                'expected_role' => null,
                'exception' => '\Elvo\Mvc\Authentication\Role\Exception\InvalidRoleDataException'
            )
        );
    }


    public function roleData()
    {
        return array(
            array(
                'role_data' => 'B-13000-ZAMESTNANEC-NEAKADEMICKY:13000 - FEL - Fakulta elektrotechnická - neakademický zaměstnanec:13000:76:13000 - FEE - Faculty of Electrical Engineering - Non-academic employee;B-00000-ZAMESTNANEC-NEAKADEMICKY:00000 - ČVUT - České vysoké učení technické v Praze - neakademický zaměstnanec:00000:76:00000 - CTU - Czech Technical University in Prague - Non-academic employee',
                'department_code' => '13000',
                'expected_roles' => array()
            ),
            array(
                'role_data' => 'B-11000-STUDENT:BLAH - BLAH;B-13000-ZAMESTNANEC-NEAKADEMICKY:BLAH - BLAH',
                'department_code' => '13000',
                'expected_role' => array()
            ),
            array(
                'role_data' => 'B-11000-STUDENT:BLAH - BLAH;B-13000-ZAMESTNANEC-NEAKADEMICKY:BLAH - BLAH',
                'department_code' => '11000',
                'expected_role' => array(
                    'student'
                )
            ),
            array(
                'role_data' => 'B-11000-STUDENT:BLAH - BLAH;B-13000-ZAMESTNANEC-AKADEMICKY:BLAH - BLAH',
                'department_code' => '13000',
                'expected_role' => array(
                    'academic'
                )
            ),
            array(
                'role_data' => 'B-11000-STUDENT:BLAH - BLAH;B-11000-ZAMESTNANEC-AKADEMICKY:BLAH - BLAH',
                'department_code' => '11000',
                'expected_role' => array(
                    'student',
                    'academic'
                )
            ),
            array(
                'role_data' => 'B-11000-STUDENT:BLAH - BLAH;B-12000-ZAMESTNANEC-AKADEMICKY:BLAH - BLAH',
                'department_code' => array(
                    '11000',
                    '12000'
                ),
                'expected_role' => array(
                    'student',
                    'academic'
                )
            )
        );
    }
}