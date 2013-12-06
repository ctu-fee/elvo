<?php

namespace ElvoTest\Domain\Vote;

use Elvo\Domain\Vote\VoteManager;
use Elvo\Util\Options;
use Elvo\Domain\Entity\Chamber;


class VoteManagerTest extends \PHPUnit_Framework_TestCase
{

    protected $manager;

    protected $dateFormat = 'Y-m-d H:i:s';


    public function setUp()
    {
        $this->manager = new VoteManager();
    }


    public function testSetOptions()
    {
        $options = new Options();
        $this->manager->setOptions($options);
        $this->assertSame($options, $this->manager->getOptions());
    }


    public function testGetStartTime()
    {
        $time = '2013-11-26 19:00:00';
        $options = new Options(array(
            'start_time' => $time
        ));
        $this->manager->setOptions($options);
        
        $startTime = $this->manager->getStartTime();
        $this->assertInstanceOf('DateTime', $startTime);
        $this->assertSame($time, $startTime->format($this->dateFormat));
    }


    public function testGetEndTime()
    {
        $time = '2013-11-27 19:00:00';
        $options = new Options(array(
            'end_time' => $time
        ));
        $this->manager->setOptions($options);
        
        $startTime = $this->manager->getEndTime();
        $this->assertInstanceOf('DateTime', $startTime);
        $this->assertSame($time, $startTime->format($this->dateFormat));
    }


    public function testIsVotingEnabled()
    {
        $enabled = true;
        $options = new Options(array(
            'enabled' => $enabled
        ));
        $this->manager->setOptions($options);
        $this->assertTrue($this->manager->isVotingEnabled());
    }


    public function testGetMaxCandidatesForChamberWithUnsetValue()
    {
        $this->setExpectedException('Elvo\Util\Exception\MissingOptionException');
        
        $this->manager->getMaxCandidatesForChamber(Chamber::academic());
    }


    public function testGetMaxCandidatesForChamber()
    {
        $studentChamber = Chamber::student();
        $academicChamber = Chamber::academic();
        
        $this->manager->setOptions(new Options(array(
            VoteManager::OPT_CHAMBER_MAX_CANDIDATES => array(
                $studentChamber->getCode() => 3,
                $academicChamber->getCode() => 5
            )
        )));
        
        $this->assertSame(3, $this->manager->getMaxCandidatesForChamber($studentChamber));
        $this->assertSame(5, $this->manager->getMaxCandidatesForChamber($academicChamber));
    }
    
    public function testGetMaxVotesForChamberWithUnsetValue()
    {
        $this->setExpectedException('Elvo\Util\Exception\MissingOptionException');
    
        $this->manager->getMaxVotesForChamber(Chamber::academic());
    }


    public function testGetMaxVotesForChamber()
    {
        $studentChamber = Chamber::student();
        $academicChamber = Chamber::academic();
        
        $this->manager->setOptions(new Options(array(
            VoteManager::OPT_CHAMBER_MAX_VOTES => array(
                $studentChamber->getCode() => 2,
                $academicChamber->getCode() => 4
            )
        )));
        
        $this->assertSame(2, $this->manager->getMaxVotesForChamber($studentChamber));
        $this->assertSame(4, $this->manager->getMaxVotesForChamber($academicChamber));
    }


    public function testGetElectoralName()
    {
        $this->manager->setOptions(new Options(array(
            VoteManager::OPT_ELECTORAL_NAME => 'FOO'
        )));
        $this->assertSame('FOO', $this->manager->getElectoralName());
    }


    public function testGetContactEmail()
    {
        $this->manager->setOptions(new Options(array(
            VoteManager::OPT_CONTACT_EMAIL => 'foo@example.cz'
        )));
        $this->assertSame('foo@example.cz', $this->manager->getContactEmail());
    }


    /**
     * @dataProvider isVotingActiveProvider
     */
    public function testIsVotingActive($enabled, $currentTime, $startTime, $endTime, $expected)
    {
        $currentTime = new \DateTime($currentTime);
        $options = new Options(array(
            'enabled' => $enabled,
            'start_time' => $startTime,
            'end_time' => $endTime
        ));
        
        $this->manager->setOptions($options);
        $this->assertSame($expected, $this->manager->isVotingActive($currentTime));
    }


    /**
     * @dataProvider getVotingStatusProvider
     */
    public function testGetVotingStatus($currentTime, $startTime, $endTime, $expected)
    {
        $currentTime = new \DateTime($currentTime);
        $options = new Options(array(
            'start_time' => $startTime,
            'end_time' => $endTime
        ));
        
        $this->manager->setOptions($options);
        $this->assertSame($expected, $this->manager->getVotingStatus($currentTime));
    }
    
    /*
     * 
     */
    public function isVotingActiveProvider()
    {
        return array(
            array(
                'enabled' => false,
                'currentTime' => '2013-11-25 10:00:00',
                'startTime' => '2013-11-23 10:00:00',
                'endTime' => '2013-11-28 10:00:00',
                'expected' => false
            ),
            array(
                'enabled' => true,
                'currentTime' => '2013-11-25 10:00:00',
                'startTime' => '2013-11-23 10:00:00',
                'endTime' => '2013-11-28 10:00:00',
                'expected' => true
            ),
            array(
                'enabled' => true,
                'currentTime' => '2013-11-23 10:00:00',
                'startTime' => '2013-11-25 10:00:00',
                'endTime' => '2013-11-28 10:00:00',
                'expected' => false
            ),
            array(
                'enabled' => true,
                'currentTime' => '2013-11-30 10:00:00',
                'startTime' => '2013-11-23 10:00:00',
                'endTime' => '2013-11-28 10:00:00',
                'expected' => false
            )
        );
    }


    public function getVotingStatusProvider()
    {
        return array(
            array(
                'currentTime' => '2013-11-25 10:00:00',
                'startTime' => '2013-11-23 10:00:00',
                'endTime' => '2013-11-28 10:00:00',
                'expected' => VoteManager::STATUS_RUNNING
            ),
            array(
                'currentTime' => '2013-11-23 10:00:00',
                'startTime' => '2013-11-25 10:00:00',
                'endTime' => '2013-11-28 10:00:00',
                'expected' => VoteManager::STATUS_NOT_STARTED
            ),
            array(
                'currentTime' => '2013-11-30 10:00:00',
                'startTime' => '2013-11-23 10:00:00',
                'endTime' => '2013-11-28 10:00:00',
                'expected' => VoteManager::STATUS_FINISHED
            )
        );
    }
}