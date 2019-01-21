<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Interop\Container\ContainerInterface;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

return [
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
            'opening' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/opening',
                    'defaults' => [
                        'controller' => Controller\ChessGamesController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'getMove' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/getMove/:url',
                    'defaults' => [
                        'controller' => Controller\ChessGamesController::class,
                        'action' => 'getMove'
                    ]
                ]
            ],
            'example' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/example',
                    'defaults' => [
                        'controller' => Controller\ChessGamesController::class,
                        'action' => 'example'
                    ]
                ]
            ]
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class,
            Controller\ChessGamesController::class => function(ContainerInterface $container, $requestedName) {
                $chessGamesService = $container->get(Service\ChessGames::class);
                return new Controller\ChessGamesController($chessGamesService);
            },
        ],
    ],
    'service_manager' => [
        'factories' => [
            Service\ChessGames::class => Service\Factory\ChessGames::class,
            'ChessGamesClient' => Service\Factory\ChessGamesClient::class
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
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
];
