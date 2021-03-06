<?php

namespace ElvoTest\Domain\Entity\Collection;

use Elvo\Domain\Entity\Collection\EncryptedVoteCollection;


class EncryptedVoteCollectionTest extends \PHPUnit_Framework_Testcase
{

    protected $collection;


    public function setUp()
    {
        $this->collection = new EncryptedVoteCollection();
    }


    public function testAppendWithInvalidItem()
    {
        $this->setExpectedException('Elvo\Domain\Entity\Collection\Exception\InvalidItemException');
        
        $candidate = new \stdClass();
        $this->collection->append($candidate);
    }
}