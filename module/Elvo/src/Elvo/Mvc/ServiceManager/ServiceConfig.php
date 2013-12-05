<?php

namespace Elvo\Mvc\ServiceManager;

use Zend\Db;
use Zend\EventManager\EventManager;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\ServiceManager;
use Zend\Authentication\AuthenticationService;
use Elvo\Domain;
use Elvo\Util\Options;
use Elvo\Util\Environment;
use Elvo\Mvc\Authentication\IdentityFactory;
use Elvo\Mvc\Listener\DispatchListener;
use Elvo\Mvc\Listener\ApplicationEventsListener;
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
                $identityFactory = new IdentityFactory();
                $identityFactory->setRoleExtractor($sm->get('Elvo\AuthenticationRoleExtractor'));
                
                return $identityFactory;
            },
            
            'Elvo\AuthenticationRoleExtractor' => function (ServiceManager $sm)
            {
                $config = $sm->get('Config');
                if (! isset($config['elvo']['authentication']['role_extractor']['class'])) {
                    throw new Exception\MissingConfigException("Missing config 'elvo/authentication/role_extractor'");
                }
                
                $roleExtractorClass = $config['elvo']['authentication']['role_extractor']['class'];
                
                $options = array();
                if (isset($config['elvo']['authentication']['role_extractor']['options']) && is_array($config['elvo']['authentication']['role_extractor']['options'])) {
                    $options = $config['elvo']['authentication']['role_extractor']['options'];
                }
                
                $roleExtractor = new $roleExtractorClass(new Options($options));
                
                return $roleExtractor;
            },
            
            'Elvo\AuthenticationService' => function (ServiceManager $sm)
            {
                $config = $sm->get('Config');
                if (! isset($config['elvo']['authentication']['adapter'])) {
                    throw new Exception\MissingConfigException("Missing config 'elvo/authentication/adapter'");
                }
                
                $authAdapterConfig = $config['elvo']['authentication']['adapter'];
                
                if (! isset($authAdapterConfig['adapter'])) {
                    throw new Exception\MissingConfigException("Missing config 'elvo/authentication/adapter/adapter'");
                }
                
                $adapterClass = $authAdapterConfig['adapter'];
                $options = array();
                if (isset($authAdapterConfig['options']) && is_array($authAdapterConfig['options'])) {
                    $options = $authAdapterConfig['options'];
                }
                
                $adapter = new $adapterClass($options, null, $sm->get('Elvo\IdentityFactory'));
                
                $authService = new AuthenticationService();
                $authService->setAdapter($adapter);
                
                return $authService;
            },
            

            
            /*
             * ----------------------
             * Domain layer services
             * ----------------------
             */
            'Elvo\CandidateService' => function (ServiceManager $sm)
            {
                $config = $sm->get('Config');
                if (! isset($config['elvo']['candidates']['options']) || ! is_array($config['elvo']['candidates']['options'])) {
                    throw new Exception\MissingConfigException("Missing config 'elvo/candidates/file'");
                }
                
                $options = new Options($config['elvo']['candidates']['options']);
                $candidateFactory = $sm->get('Elvo\Domain\CandidateFactory');
                $voteManager = $sm->get('Elvo\Domain\VoteManager');
                
                $candidateService = new Domain\Candidate\Service\Service($candidateFactory, $voteManager, $options);
                return $candidateService;
            },
            
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
                $voteManager = new Domain\Vote\VoteManager($options);
                return $voteManager;
            },
            
            'Elvo\Domain\VoteFactory' => function (ServiceManager $sm)
            {
                return new Domain\Entity\Factory\VoteFactory();
            },
            
            'Elvo\Domain\VoteValidatorFactory' => function (ServiceManager $sm)
            {
                $voteValidatorFactory = new Domain\Vote\Validator\ValidatorFactory();
                
                return $voteValidatorFactory;
            },
            
            'Elvo\Domain\VoteValidator' => function (ServiceManager $sm)
            {
                $config = $sm->get('Config');
                if (! isset($config['elvo']['vote_validator']['validators']) && ! is_array($config['elvo']['vote_validator']['validators'])) {
                    throw new Exception\MissingConfigException("Missing config 'elvo/vote_validator/validators'");
                }
                
                $validatorsConfig = $config['elvo']['vote_validator']['validators'];
                $validatorFactory = $sm->get('Elvo\Domain\VoteValidatorFactory');
                
                $chainValidator = $validatorFactory->createChainValidator();
                foreach ($validatorsConfig as $validatorConfig) {
                    $validator = $validatorFactory->createValidator($validatorConfig);
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