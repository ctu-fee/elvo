<?php

namespace ElvoTest\Domain\Entity;

use Elvo\Domain\Entity\EncryptedVote;


class EncryptedVoteTest extends \PHPUnit_Framework_Testcase
{


    public function testConstructor()
    {
        $data = 'qwerty';
        $eVote = new EncryptedVote($data);
        $this->assertSame($data, (string) $eVote);
    }
}