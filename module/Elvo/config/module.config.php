<?php
return array(
    
    'router' => array(
        'routes' => array(
            
            'index' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/',
                    'defaults' => array(
                        'controller' => 'Elvo\Controller\IndexController',
                        'action' => 'index'
                    )
                )
            ),
            
            'role' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/vote/role',
                    'defaults' => array(
                        'controller' => 'Elvo\Controller\VoteController',
                        'action' => 'role'
                    )
                )
            ),
            
            'form' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/vote/form',
                    'defaults' => array(
                        'controller' => 'Elvo\Controller\VoteController',
                        'action' => 'form'
                    )
                )
            ),
            
            'confirm' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/vote/confirm',
                    'defaults' => array(
                        'controller' => 'Elvo\Controller\VoteController',
                        'action' => 'confirm'
                    )
                )
            ),
            
            'submit' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/vote/submit',
                    'defaults' => array(
                        'controller' => 'Elvo\Controller\VoteController',
                        'action' => 'submit'
                    )
                )
            ),
            
            'status' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/vote/status',
                    'defaults' => array(
                        'controller' => 'Elvo\Controller\VoteController',
                        'action' => 'status'
                    )
                )
            ),
            
            'error' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/vote/error',
                    'defaults' => array(
                        'controller' => 'Elvo\Controller\VoteController',
                        'action' => 'error'
                    )
                )
            )
        )
    ),
    
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        
        'template_map' => array(
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            // 'elvo/index/index' => __DIR__ . '/../view/elvo/index/index.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml'
        ),
        
        'template_path_stack' => array(
            __DIR__ . '/../view'
        )
    ),
    
    'translator' => array(
        'locale' => 'cs',
        'translation_file_patterns' => array(
            array(
                'type' => 'PhpArray',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.php'
            )
        )
    ),
    
    'elvo' => array(
        
        'environment' => array(
            'mode' => 'devel'
        ),
        
        'vote_manager' => array(
            'options' => array(
                'enabled' => true,
                'start_time' => '2013-11-23 08:00:00',
                'end_time' => '2013-11-28 14:00:00'
            )
        ),
        
        'db' => array(
            'driver' => 'Pdo_Sqlite',
            'database' => '/tmp/elvo.sqlite'
        ),
        
        'authentication' => array(
            'adapter' => 'ZfcShib\Authentication\Adapter\Dummy',
            'options' => array(
                'user_data' => array(
                    'voter_id' => '123456',
                    'voter_roles' => 3
                )
            )
        ),
        
        'candidates' => array(
            'options' => array(
                'candidates' => __DIR__ . '/../data/candidates.php',
                'chamber_count' => array(
                    'academic' => 3,
                    'student' => 2
                )
            )
        ),
        
        'vote_validator' => array(
            'validators' => array(
                'candidate_count' => array(
                    'validator' => 'Elvo\Domain\Vote\Validator\CandidateCountValidator',
                    'options' => array(
                        'chamber_count' => array(
                            'academic' => 3,
                            'student' => 2
                        )
                    )
                ),
                'voter_role' => array(
                    'validator' => 'Elvo\Domain\Vote\Validator\VoterRoleValidator',
                    'options' => array()
                )
            )
        ),
        
        'vote_encryptor' => array(
            'encryptor' => 'Elvo\Domain\Vote\OpensslEncryptor',
            'options' => array(
                'certificate' => __DIR__ . '/../../../data/ssl/crypt.crt',
                'private_key' => __DIR__ . '/../../../data/ssl/crypt.key'
            )
        )
    )
);