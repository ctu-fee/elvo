<?php

namespace Elvo\Mvc\ServiceManager;

use Elvo\Util\Environment;
use Zend\Db;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\ServiceManager;
use Zend\Authentication\AuthenticationService;
use Elvo\Domain;
use Elvo\Mvc\Authentication\IdentityFactory;
use Elvo\Mvc\Candidate\CandidateService;
use Elvo\Util\Options;
use Elvo\Mvc\Listener\DispatchListener;
use Elvo\Domain\Vote\VoteManager;
use Elvo\Mvc\Listener\ApplicationEventsListener;
use Zend\EventManager\EventManager;
use Monolog\Logger;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Formatter\LineFormatter;


class ServiceConfig extends Config
{


    public function getFactories()
    {
        return array(
        
            /*
             * ------------------
             * MVC layer services
             * ------------------
             */
            'Elvo\EventManager' => function (ServiceManager $sm)
            {
                $events = new EventManager();
                return $events;
            },
            
            'Elvo\Logger' => function (ServiceManager $sm)
            {
                $logger = new Logger('elvo');
                
                $handler = new ErrorLogHandler();
                $handler->setFormatter(new LineFormatter('[%datetime%] %channel%.%level_name%: %message%'));
                
                $logger->pushHandler($handler);
                
                return $logger;
            },
            
            'Elvo\Environment' => function (ServiceManager $sm)
            {
                $config = $sm->get('Config');
                $options = array();
                if (isset($config['elvo']['environment']) && is_array($config['elvo']['environment'])) {
                    $options = $config['elvo']['environment'];
                }
                
                $environment = new Environment($options);
                return $environment;
            },
            
            'Elvo\DispatchListener' => function (ServiceManager $sm)
            {
                $dispatchListener = new DispatchListener();
                $dispatchListener->setLogger($sm->get('Elvo\Logger'));
                
                return $dispatchListener;
            },
            
            'Elvo\ApplicationEventsListener' => function (ServiceManager $sm)
            {
                $listener = new ApplicationEventsListener();
                $listener->setLogger($sm->get('Elvo\Logger'));
                
                return $listener;
            },
            
            'Elvo\Translator' => 'Zend\I18n\Translator\TranslatorServiceFactory',
            
            'Elvo\IdentityFactory' => function (ServiceManager $sm)
            {
                return new IdentityFactory();
            },
            
            'Elvo\AuthenticationService' => function (ServiceManager $sm)
            {
                $config = $sm->get('Config');
                if (! isset($config['elvo']['authentication']['adapter'])) {
                    throw new Exception\MissingConfigException("Missing config 'elvo/authentication/adapter'");
                }
                
                $adapterClass = $config['elvo']['authentication']['adapter'];
                $options = array();
                if (isset($config['elvo']['authentication']['options']) && is_array($config['elvo']['authentication']['options'])) {
                    $options = $config['elvo']['authentication']['options'];
                }
                
                $adapter = new $adapterClass($options, null, $sm->get('Elvo\IdentityFactory'));
                
                $authService = new AuthenticationService();
                $authService->setAdapter($adapter);
                
                return $authService;
            },
            
            'Elvo\CandidateService' => function (ServiceManager $sm)
            {
                $config = $sm->get('Config');
                if (! isset($config['elvo']['candidates']['options']) || ! is_array($config['elvo']['candidates']['options'])) {
                    throw new Exception\MissingConfigException("Missing config 'elvo/candidates/file'");
                }
                
                $options = new Options($config['elvo']['candidates']['options']);
                
                $candidateService = new CandidateService($sm->get('Elvo\Domain\CandidateFactory'), $options);
                return $candidateService;
            },
            
            /*
             * ----------------------
             * Domain layer services
             * ----------------------
             */
            'Elvo\Domain\VoteService' => function (ServiceManager $sm)
            {
                $voteManager = $sm->get('Elvo\Domain\VoteManager');
                $voteFactory = $sm->get('Elvo\Domain\VoteFactory');
                $voteValidator = $sm->get('Elvo\Domain\VoteValidator');
                $voteEncryptor = $sm->get('Elvo\Domain\VoteEncryptor');
                $voteStorage = $sm->get('Elvo\Domain\VoteStorage');
                
                $voteService = new Domain\Vote\Service\Service($voteManager, $voteFactory, $voteValidator, $voteEncryptor, $voteStorage);
                return $voteService;
            },
            
            'Elvo\Domain\VoteManager' => function (ServiceManager $sm)
            {
                $config = $sm->get('Config');
                if (! isset($config['elvo']['vote_manager']['options']) || ! is_array($config['elvo']['vote_manager']['options'])) {
                    throw new Exception\MissingConfigException("Missing config 'elvo/vote_manager/options'");
                }
                
                $options = new Options($config['elvo']['vote_manager']['options']);
                $voteManager = new VoteManager($options);
                return $voteManager;
            },
            
            'Elvo\Domain\VoteFactory' => function (ServiceManager $sm)
            {
                return new Domain\Entity\Factory\VoteFactory();
            },
            
            'Elvo\Domain\VoteValidator' => function (ServiceManager $sm)
            {
                $config = $sm->get('Config');
                if (! isset($config['elvo']['vote_validator']['validators']) && ! is_array($config['elvo']['vote_validator']['validators'])) {
                    throw new Exception\MissingConfigException("Missing config 'elvo/vote_validator/validators'");
                }
                
                // FIXME - move to factory class and add tests
                $validatorsConfig = $config['elvo']['vote_validator']['validators'];
                $chainValidator = new Domain\Vote\Validator\ChainValidator();
                foreach ($validatorsConfig as $validatorConfig) {
                    if (! isset($validatorConfig['validator'])) {
                        throw new Exception\MissingConfigException("Missing validator field in 'elvo/vote_validator/validators' config");
                    }
                    
                    $validatorClass = $validatorConfig['validator'];
                    if (! class_exists($validatorClass)) {
                        throw new Exception\UndefinedClassException(sprintf("Undefined vote validator class '%s'", $validatorClass));
                    }
                    
                    $options = array();
                    if (isset($validatorConfig['options']) && is_array($validatorConfig['options'])) {
                        $options = $validatorConfig['options'];
                    }
                    
                    $validator = new $validatorClass(new Options($options));
                    $chainValidator->addValidator($validator);
                }
                
                return $chainValidator;
            },
            
            'Elvo\Domain\VoteEncryptor' => function (ServiceManager $sm)
            {
                $config = $sm->get('Config');
                if (! isset($config['elvo']['vote_encryptor']) && ! is_array($config['elvo']['vote_encryptor'])) {
                    throw new Exception\MissingConfigException("Missing config 'elvo/vote_encryptor'");
                }
                
                // FIXME - move to factory class and add tests
                $encryptorConfig = $config['elvo']['vote_encryptor'];
                if (! isset($encryptorConfig['encryptor'])) {
                    throw new Exception\MissingConfigException("Missing encryptor field in 'elvo/vote_encryptor' config");
                }
                
                $encryptorClass = $encryptorConfig['encryptor'];
                if (! class_exists($encryptorClass)) {
                    throw new Exception\UndefinedClassException(sprintf("Undefined encryptor class '%s'", $encryptorClass));
                }
                
                $options = array();
                if (isset($encryptorConfig['options']) && is_array($encryptorConfig['options'])) {
                    $options = $encryptorConfig['options'];
                }
                
                $encryptor = new $encryptorClass(new Options($options));
                return $encryptor;
            },
            
            'Elvo\Domain\VoteStorage' => function (ServiceManager $sm)
            {
                $storage = new Domain\Vote\Storage\GenericDb($sm->get('Elvo\Db'));
                return $storage;
            },
            
            'Elvo\Domain\CandidateFactory' => function (ServiceManager $sm)
            {
                return new Domain\Entity\Factory\CandidateFactory();
            },
            
            /*
             * -------------------
             * Storage/persistence
             * -------------------
             */
            'Elvo\Db' => function (ServiceManager $sm)
            {
                $config = $sm->get('Config');
                if (! isset($config['elvo']['db']) || ! is_array($config['elvo']['db'])) {
                    throw new Exception\MissingConfigException("Missing config 'elvo/db'");
                }
                
                $dbAdapter = new Db\Adapter\Adapter($config['elvo']['db']);
                return $dbAdapter;
            }
        );
    }
}