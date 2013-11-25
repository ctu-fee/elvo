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
                    'route' => '/role',
                    'defaults' => array(
                        'controller' => 'Elvo\Controller\VoteController',
                        'action' => 'role'
                    )
                )
            ),
            
            'form' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/form',
                    'defaults' => array(
                        'controller' => 'Elvo\Controller\VoteController',
                        'action' => 'form'
                    )
                )
            ),
            
            'confirm' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/confirm',
                    'defaults' => array(
                        'controller' => 'Elvo\Controller\VoteController',
                        'action' => 'confirm'
                    )
                )
            ),
            
            'submit' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/submit',
                    'defaults' => array(
                        'controller' => 'Elvo\Controller\VoteController',
                        'action' => 'submit'
                    )
                )
            ),
            
            'result' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/result',
                    'defaults' => array(
                        'controller' => 'Elvo\Controller\VoteController',
                        'action' => 'result'
                    )
                )
            ),
            
            'error' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/error',
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
    )
    
);