<?php

use Elvo\Domain\Entity\Factory\VoteFactory;
use Elvo\Domain\Vote\Service\Service;
use Elvo\Domain\Vote\VoteManager;
use Elvo\Domain\Vote\Validator\ChainValidator;
use Elvo\Util\Options;
use Elvo\Domain\Vote\OpensslEncryptor;
use Zend\Db;
use Zend\Loader\AutoloaderFactory;
use Elvo\Domain\Vote\Storage\GenericDb;

require __DIR__ . '/../vendor/autoload.php';

$config = array(
    'db' => array(
        'driver' => 'Pdo_Sqlite',
        'database' => '/tmp/elvo.sqlite'
    ),
    'encryptor' => array(
        'certificate' => __DIR__ . '/../data/ssl/crypt.crt',
        'private_key' => __DIR__ . '/../data/ssl/crypt.key'
    )
);

AutoloaderFactory::factory(array(
    'Zend\Loader\StandardAutoloader' => array(
        'namespaces' => array(
            'Elvo' => __DIR__ . '/../module/Elvo/src/Elvo'
        )
    )
));

$dbAdapter = new Db\Adapter\Adapter($config['db']);
$storage = new GenericDb($dbAdapter);

$encryptor = new OpensslEncryptor(new Options($config['encryptor']));

$validator = new ChainValidator();

$factory = new VoteFactory();

$manager = new VoteManager();

$service = new Service($manager, $factory, $validator, $encryptor, $storage);

$votes = $service->fetchAllVotes();
foreach ($votes as $vote) {
    /* @var $vote \Elvo\Domain\Entity\Vote */
    $candidates = $vote->getCandidates();
    $names = array();
    foreach ($candidates as $candidate) {
        $names[] = sprintf("%s %s", $candidate->getFirstName(), $candidate->getLastName());
    }
    printf("[%s] %s\n", $vote->getVoterRole(), implode(', ', $names));
}

// ----------
function _dump($value)
{
    error_log(print_r($value, true));
}


