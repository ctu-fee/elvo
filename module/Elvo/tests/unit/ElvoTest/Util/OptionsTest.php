<?php

namespace ElvoTest\Util;

use Elvo\Util\Options;


class OptionsTest extends \PHPUnit_Framework_Testcase
{


    public function testGetWithExistingIndex()
    {
        $options = new Options(array(
            'foo' => 'bar'
        ));
        
        $this->assertSame('bar', $options->get('foo'));
    }


    public function testGetWithDefaultValue()
    {
        $options = new Options();
        $this->assertSame('bar', $options->get('foo', 'bar'));
    }


    public function testGetNonExistentIndex()
    {
        $options = new Options();
        $this->assertNull($options->get('foo'));
    }


    public function testSet()
    {
        $options = new Options();
        $options->set('foo', 'bar');
        $this->assertSame('bar', $options->get('foo'));
    }


    public function testAddDefaultValues()
    {
        $options = new Options(array(
            'item1' => 'value1',
            'item2' => 'value2'
        ));
        
        $options->addDefaultValues(array(
            'item1' => 'default1',
            'item3' => 'default3'
        ));
        
        $expected = array(
            'item1' => 'value1',
            'item2' => 'value2',
            'item3' => 'default3'
        );
        
        $this->assertEquals($expected, $options->getArrayCopy());
    }
}