<?php

namespace ElvoTest\Domain\Entity\Collection;

use Elvo\Domain\Entity\Collection\Exception\InvalidItemException;


class AbstractCollectionTest extends \PHPUnit_Framework_Testcase
{


    public function testConstructor()
    {
        $item1 = new \stdClass();
        $item2 = new \stdClass();
        
        $data = array(
            $item1,
            $item2
        );
        
        $collection = $this->getCollectionMock($data);
        $i = 0;
        foreach ($collection as $item) {
            $this->assertSame($data[$i ++], $item);
        }
    }


    public function testGetIterator()
    {
        $collection = $this->getCollectionMock();
        $iterator = $collection->getIterator();
        $this->assertInstanceOf('ArrayIterator', $iterator);
    }


    public function testAppend()
    {
        $item1 = new \stdClass();
        $item2 = new \stdClass();
        
        $data = array(
            $item1,
            $item2
        );
        
        $collection = $this->getCollectionMock();
        $collection->append($item1);
        $collection->append($item2);
        
        $i = 0;
        foreach ($collection as $item) {
            $this->assertSame($data[$i ++], $item);
        }
    }


    public function testCount()
    {
        $item1 = new \stdClass();
        $item2 = new \stdClass();
        
        $collection = $this->getCollectionMock();
        $this->assertSame(0, $collection->count());
        
        $collection->append($item1);
        $this->assertSame(1, $collection->count());
        $this->assertSame($item1, $collection->offsetGet(0));
        
        $collection->append($item2);
        $this->assertSame(2, $collection->count());
        $this->assertSame($item2, $collection->offsetGet(1));
    }


    public function testOffsetSetWithInvalidValue()
    {
        $this->setExpectedException('Elvo\Domain\Entity\Collection\Exception\InvalidItemException', 'invalid_item');
        
        $exception = new InvalidItemException('invalid_item');
        $item = new \stdClass();
        
        $collection = $this->getCollectionMock();
        $collection->expects($this->once())
            ->method('validate')
            ->with($item)
            ->will($this->throwException($exception));
        
        $collection->append($item);
    }
    
    /*
     * 
     */
    /**
     * @param array $data
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getCollectionMock(array $data = array())
    {
        $collection = $this->getMockBuilder('Elvo\Domain\Entity\Collection\AbstractCollection')
            ->setConstructorArgs(array(
            $data
        ))
            ->getMockForAbstractClass();
        
        return $collection;
    }
}