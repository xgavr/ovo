<?php
namespace Blank;

use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class,
        ],
    ],
    'router' => [
        'routes' => [
            'torg12' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/torg12[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller'    => Controller\IndexController::class,
                        'action'        => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    // You can place additional routes that match under the
                    // route defined above here.
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'Blank' => __DIR__ . '/../view',
        ],
    ],
    'access_filter' => [
        'controllers' => [
            \Blank\Controller\IndexController::class => [
                // Allow access to authenticated users.
                ['actions' => '*', 'allow' => '*']
            ],
        ],
    ],    
];
