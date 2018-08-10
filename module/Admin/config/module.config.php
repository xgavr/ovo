<?php
namespace Admin;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
        'routes' => [
            'admin' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/admin[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller'    => Controller\IndexController::class,
                        'action'        => 'index',
                    ],
                ],
            ],
            'proc' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/proc[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller'    => Controller\ProcessingController::class,
                        'action'        => 'index',
                    ],
                ],
            ],
            'telegram' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/telegramm[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller'    => Controller\TelegrammController::class,
                        'action'        => 'index',
                    ],
                ],
            ],
        ],
    ],
    'access_filter' => [
        'controllers' => [
            Controller\IndexController::class => [
                // Allow access to authenticated users.
                ['actions' => '*', 'allow' => '+admin.manage'],
                ['actions' => 'telegramm-hook', 'allow' => '*']
            ],
            Controller\PostController::class => [
                // Allow access to authenticated users.
                ['actions' => '*', 'allow' => '+admin.manage']
            ],
            Controller\ProcessingController::class => [
                // Allow access to all users.
                ['actions' => '*', 'allow' => '*']
            ],
            Controller\TelegramController::class => [
                // Allow access to authenticated users.
                ['actions' => ['index', 'set', 'unset'], 'allow' => '+admin.manage'],
                ['actions' => ['hook'], 'allow' => '*']
            ],
        ],
    ],    
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class,
            Controller\ProcessingController::class => Controller\Factory\ProcessingControllerFactory::class,
            Controller\TelegramController::class => Controller\Factory\TelegramControllerFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            Service\AdminManager::class => Service\Factory\AdminManagerFactory::class,
            Service\FtpManager::class => Service\Factory\FtpManagerFactory::class,
            Service\PostManager::class => Service\Factory\PostManagerFactory::class,
            Service\SmsManager::class => Service\Factory\SmsManagerFactory::class,
            Service\TelegramManager::class => Service\Factory\TelegramManagerFactory::class,
        ],
    ],    
    'view_manager' => [
        'template_path_stack' => [
            'Admin' => __DIR__ . '/../view',
        ],
    ],
];
