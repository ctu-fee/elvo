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
            ),
            
            'autherror' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/autherror',
                    'defaults' => array(
                        'controller' => 'Elvo\Controller\IndexController',
                        'action' => 'autherror'
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
                'end_time' => '2013-11-28 14:00:00',
                'chamber_max_candidates' => array(
                    'academic' => 3,
                    'student' => 2
                ),
                'chamber_max_votes' => array(
                    'academic' => 2,
                    'student' => 1
                ),
                'electoral_name' => 'FEL',
                'contact_email' => 'volby@example.cz'
            )
        ),
        
        'db' => array(
            'driver' => 'Pdo_Sqlite',
            'database' => '/tmp/elvo.sqlite'
        ),
        
        'authentication' => array(
            'adapter' => array(
                'adapter' => 'ZfcShib\Authentication\Adapter\Dummy',
                'options' => array(
                    'user_data' => array(
                        'voter_id' => '123456',
                        'voter_roles' => 'B-13000-STUDENT;B-13000-ZAMESTNANEC-AKADEMICKY'
                    )
                )
            ),
            'role_extractor' => array(
                'class' => 'Elvo\Mvc\Authentication\Role\CvutRoleExtractor',
                'options' => array(
                    'department_code' => array(
                        '13000'
                    )
                )
            )
        ),
        
        'candidate_storage' => array(
            'storage' => 'Elvo\Domain\Candidate\Storage\JsonInFile',
            'options' => array(
                'file_path' => __DIR__ . '/../data/candidates.json'
            )
        ),
        
        'vote_validator' => array(
            'validators' => array(
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