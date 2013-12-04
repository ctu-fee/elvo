<?php

namespace ElvoTest\Mvc\Authentication\Role;

use Elvo\Mvc\Authentication\Role\CvutRoleExtractor;


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
     * @dataProvider roleComplexValues
     */
    public function testExtractRoleCodeFromComplexValue($complex, $extracted)
    {
        $this->assertSame($extracted, $this->extractor->extractRoleCodeFromComplexValue($complex));
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
}