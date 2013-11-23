<?php

namespace ElvoTest\Domain\Entity;

use Elvo\Domain\Entity\EncryptedVote;


class EncryptedVoteTest extends \PHPUnit_Framework_Testcase
{


    public function testConstructor()
    {
        $data = 'qwerty';
        $key = 'secret';
        $eVote = new EncryptedVote($data, $key);
        $this->assertSame($data, $eVote->getData());
        $this->assertSame($key, $eVote->getKey());
    }
}