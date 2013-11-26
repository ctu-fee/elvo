<?php

namespace ElvoFuncTest;

use Elvo\Domain\Entity\EncryptedVote;
use Zend\Db;
use Elvo\Domain\Vote\Storage;


class SqliteVoteStorageTest extends \PHPUnit_Framework_Testcase
{

    protected $dbAdapter;


    public function setUp()
    {
        $this->dbAdapter = new Db\Adapter\Adapter(array(
            'driver' => 'Pdo_Sqlite',
            'database' => ':memory:'
        ));
        
        // $initQuery = file_get_contents(ELVO_DB_SCRIPTS_DIR . '/sqlite/init_tables.sql');
        // $this->dbAdapter->query($initQuery, Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        $this->dbAdapter->query('CREATE TABLE vote ( id INTEGER PRIMARY KEY, data TEXT NOT NULL, key TEXT NOT NULL );', Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        $this->dbAdapter->query('CREATE TABLE voter (  voter_id VARCHAR(64) PRIMARY KEY );', Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
    }


    public function tearDown()
    {}


    public function testStoreFetchVote()
    {
        $encryptedVoteData = $this->getEncryptedVoteData();
        
        $storage = new Storage\GenericDb($this->dbAdapter);
        
        foreach ($encryptedVoteData as $rawItem) {
            $encryptedVote = new EncryptedVote($rawItem[0], $rawItem[1]);
            $storage->save($encryptedVote);
        }
        
        $encryptedVotes = $storage->fetchAll();
        $resultData = array();
        foreach ($encryptedVotes as $encryptedVote) {
            $resultData[] = array(
                $encryptedVote->getData(),
                $encryptedVote->getKey()
            );
        }
        
        $this->assertEquals($encryptedVoteData, $resultData);
    }


    public function testStoreFetchVoterId()
    {
        $voterIds = array(
            '111',
            '222',
            '333',
            '444'
        );
        
        $storage = new Storage\GenericDb($this->dbAdapter);
        
        foreach ($voterIds as $id) {
            $storage->saveVoterId($id);
        }
        
        foreach ($voterIds as $id) {
            $this->assertTrue($storage->existsVoterId($id));
        }
        
        $this->assertFalse($storage->existsVoterId('555'));
    }
    
    /*
     * 
     */
    protected function getEncryptedVoteData()
    {
        return array(
            array(
                'data1',
                'key1'
            ),
            array(
                'data2',
                'key2'
            ),
            array(
                'data3',
                'key3'
            )
        );
    }
}