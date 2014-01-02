<?php

namespace Elvo\Domain\Vote\Storage;

use Zend\Db;
use Elvo\Domain\Entity\EncryptedVote;
use Elvo\Domain\Entity\Collection\EncryptedVoteCollection;


/**
 * This storage uses a generic Zend\Db\Adapter\Adapter instance to store votes.
 */
class GenericDb implements StorageInterface
{

    const TABLE_VOTE = 'vote';

    const TABLE_VOTER = 'voter';

    const FIELD_ID = 'id';

    const FIELD_ENCRYPTED_VOTE = 'data';

    const FIELD_ENVELOPE_KEY = 'key';

    const FIELD_VOTER_ID = 'voter_id';

    /**
     * @var Db\Adapter\Adapter
     */
    protected $dbAdapter;

    /**
     * SQL abstraction layer.
     * @var Db\Sql\Sql
     */
    protected $sql;


    /**
     * Constructor.
     * 
     * @param Db\Adapter\Adapter $dbAdapter
     */
    public function __construct(Db\Adapter\Adapter $dbAdapter)
    {
        $this->setDbAdapter($dbAdapter);
    }


    /**
     * @param Db\Adapter\Adapter $dbAdapter
     */
    public function setDbAdapter(Db\Adapter\Adapter $dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
        $this->sql = new Db\Sql\Sql($this->dbAdapter);
    }


    /**
     * @return Db\Sql\Sql
     */
    public function getSql()
    {
        if (! $this->sql instanceof Db\Sql\Sql) {
            $this->sql = new Db\Sql\Sql($this->dbAdapter);
        }
        return $this->sql;
    }


    /**
     * @param Db\Sql\Sql $sql
     */
    public function setSql(Db\Sql\Sql $sql)
    {
        $this->sql = $sql;
    }


    /**
     * {@inheritdoc}
     * @see \Elvo\Domain\Vote\Storage\StorageInterface::saveVoterId()
     */
    public function saveVoterId($voterId)
    {
        $sql = $this->getSql();
        $insert = $sql->insert($this->getVoterTableName());
        
        $insert->values(array(
            self::FIELD_VOTER_ID => $voterId
        ));
        
        $statement = $sql->prepareStatementForSqlObject($insert);
        $statement->execute();
    }


    /**
     * {@inheritdoc}
     * @see \Elvo\Domain\Vote\Storage\StorageInterface::existsVoterId()
     */
    public function existsVoterId($voterId)
    {
        $sql = $this->getSql();
        $select = $sql->select($this->getVoterTableName());
        
        $select->columns(array(
            self::FIELD_VOTER_ID
        ));
        $select->where(array(
            self::FIELD_VOTER_ID => $voterId
        ));
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();
        
        if ($results->count() == 0) {
            return false;
        }
        
        return true;
    }


    /**
     * {@inhertidoc}
     * @see \Elvo\Domain\Vote\Storage\StorageInterface::save()
     */
    public function save(EncryptedVote $encryptedVote)
    {
        $sql = $this->getSql();
        $insert = $sql->insert($this->getVoteTableName());
        
        $encryptedVoteData = $this->dbEncdode($encryptedVote->getData());
        $encryptedVoteKey = $this->dbEncdode($encryptedVote->getKey());
        
        $insert->values(
            array(
                self::FIELD_ENCRYPTED_VOTE => $encryptedVoteData,
                self::FIELD_ENVELOPE_KEY => $encryptedVoteKey
            ));
        
        $statement = $sql->prepareStatementForSqlObject($insert);
        $statement->execute();
    }


    /**
     * {@inhertidoc}
     * @see \Elvo\Domain\Vote\Storage\StorageInterface::fetchAll()
     */
    public function fetchAll()
    {
        $sql = $this->getSql();
        $select = $sql->select($this->getVoteTableName());
        
        $select->columns(array(
            self::FIELD_ENCRYPTED_VOTE,
            self::FIELD_ENVELOPE_KEY
        ));
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();
        
        $encryptedVotes = new EncryptedVoteCollection();
        foreach ($results as $row) {
            $data = $this->dbDecode($row[self::FIELD_ENCRYPTED_VOTE]);
            $key = $this->dbDecode($row[self::FIELD_ENVELOPE_KEY]);
            $encryptedVotes->append(new EncryptedVote($data, $key));
        }
        
        return $encryptedVotes;
    }


    /**
     * {@inheritdoc}
     * @see \Elvo\Domain\Vote\Storage\StorageInterface::count()
     */
    public function count()
    {
        $sql = $this->getSql();
        $select = $sql->select($this->getVoteTableName());
        
        $select->columns(array(
            'num' => new Db\Sql\Expression('count(*)')
        ));
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $row = $result->current();
        
        return intval($row['num']);
    }


    /**
     * {@inheritdoc}
     * @see \Elvo\Domain\Vote\Storage\StorageInterface::beginTransaction()
     */
    public function beginTransaction()
    {
        $this->dbAdapter->getDriver()
            ->getConnection()
            ->beginTransaction();
    }


    /**
     * {@inheritdoc}
     * @see \Elvo\Domain\Vote\Storage\StorageInterface::commit()
     */
    public function commit()
    {
        $this->dbAdapter->getDriver()
            ->getConnection()
            ->commit();
    }


    /**
     * {@inheritdoc}
     * @see \Elvo\Domain\Vote\Storage\StorageInterface::rollback()
     */
    public function rollback()
    {
        $this->dbAdapter->getDriver()
            ->getConnection()
            ->rollback();
    }


    /**
     * Encodes data for saving to the storage.
     * 
     * @param string $value
     * @return string
     */
    protected function dbEncdode($value)
    {
        return base64_encode($value);
    }


    /**
     * Decodes fetched data from the storage.
     * 
     * @param string $value
     * @throws Exception\StorageException
     * @return string
     */
    protected function dbDecode($value)
    {
        $decodedValue = base64_decode($value);
        if (false === $decodedValue) {
            throw new Exception\StorageException('Error decoding value');
        }
        
        return $decodedValue;
    }


    protected function getVoteTableName()
    {
        return self::TABLE_VOTE;
    }


    protected function getVoterTableName()
    {
        return self::TABLE_VOTER;
    }


    protected function getEncryptedVoteFieldName()
    {
        return self::FIELD_ENCRYPTED_VOTE;
    }


    protected function getEnvelopeKeyFieldName()
    {
        return self::FIELD_ENVELOPE_KEY;
    }
}