<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

return [
    'router' => [
        'routes' => [
            'home' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'application' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/application[/:action]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'goods' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/goods[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*'
                    ],
                    'defaults' => [
                        'controller'    => Controller\GoodsController::class,
                        'action'        => 'index',
                    ],
                ],
            ],        
            'order' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/order[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*'
                    ],
                    'defaults' => [
                        'controller'    => Controller\OrderController::class,
                        'action'        => 'index',
                    ],
                ],
            ],        
            'client' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/client[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*'
                    ],
                    'defaults' => [
                        'controller'    => Controller\ClientController::class,
                        'action'        => 'index',
                    ],
                ],
            ],        
            'currency' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/currency[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*'
                    ],
                    'defaults' => [
                        'controller'    => Controller\CurrencyController::class,
                        'action'        => 'index',
                    ],
                ],
            ],        
            'supplier' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/supplier[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*'
                    ],
                    'defaults' => [
                        'controller'    => Controller\SupplierController::class,
                        'action'        => 'index',
                    ],
                ],
            ],        
            'rb' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/rb[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*'
                    ],
                    'defaults' => [
                        'controller'    => Controller\RbController::class,
                        'action'        => 'index',
                    ],
                ],
            ],    
            'about' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/about',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'about',
                    ],
                ],
            ], 
        ],
    ],
    'rbac_manager' => [
        'assertions' => [Service\RbacAssertionManager::class],
    ],        
    'controllers' => [
        'factories' => [
            Controller\ClientController::class => Controller\Factory\ClientControllerFactory::class,
            Controller\CurrencyController::class => Controller\Factory\CurrencyControllerFactory::class,
            Controller\IndexController::class => InvokableFactory::class,
            Controller\GoodsController::class => Controller\Factory\GoodsControllerFactory::class,
            Controller\OrderController::class => Controller\Factory\OrderControllerFactory::class,
            Controller\RbController::class => Controller\Factory\RbControllerFactory::class,
            Controller\SupplierController::class => Controller\Factory\SupplierControllerFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            Service\ClientManager::class => Service\Factory\ClientManagerFactory::class,
            Service\CurrencyManager::class => Service\Factory\CurrencyManagerFactory::class,
            Service\SupplierManager::class => Service\Factory\SupplierManagerFactory::class,
            Service\RbManager::class => Service\Factory\RbManagerFactory::class,
            Service\GoodsManager::class => Service\Factory\GoodsManagerFactory::class,
            Service\OrderManager::class => Service\Factory\OrderManagerFactory::class,
            Service\NavManager::class => Service\Factory\NavManagerFactory::class,
            Service\RbacAssertionManager::class => Service\Factory\RbacAssertionManagerFactory::class,            
        ],
    ],    
    'session_containers' => [
        'ContainerNamespace'
    ],
    'view_helpers' => [
        'factories' => [
            View\Helper\Menu::class => View\Helper\Factory\MenuFactory::class,
            View\Helper\Breadcrumbs::class => InvokableFactory::class,
        ],
        'aliases' => [
            'mainMenu' => View\Helper\Menu::class,
            'pageBreadcrumbs' => View\Helper\Breadcrumbs::class,
        ],
    ],    
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/Entity']
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                ]
            ]
        ]
    ],    
];
