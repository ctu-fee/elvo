<?php

namespace Elvo\Mvc\ServiceManager;

use Zend\ServiceManager\Config;
use Zend\ServiceManager\ServiceManager;
use Zend\Authentication\AuthenticationService;
use Elvo\Mvc\Authentication\IdentityFactory;
use Elvo\Domain\Entity\Factory\CandidateFactory;
use Elvo\Mvc\Candidate\CandidateService;


class ServiceConfig extends Config
{


    public function getFactories()
    {
        return array(
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
                if (! isset($config['elvo']['candidates']['file'])) {
                    throw new Exception\MissingConfigException("Missing config 'elvo/candidates/file'");
                }
                
                $candidateFile = $config['elvo']['candidates']['file'];
                
                $candidateService = new CandidateService($sm->get('Elvo\Domain\CandidateFactory'), $candidateFile);
                return $candidateService;
            },
            
            'Elvo\Domain\CandidateFactory' => function (ServiceManager $sm)
            {
                return new CandidateFactory();
            }
        );
    }
}