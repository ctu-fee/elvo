<?php

namespace ElvoTest\Domain\Entity\Collection;


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
        
        $collection->append($item2);
        $this->assertSame(2, $collection->count());
    }
    
    /*
     * 
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