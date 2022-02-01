<?php

declare(strict_types=1);

namespace Application;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Application\Controller\Plugin\CreateHttpForbiddenModel;
use Application\Controller\Plugin\CreateHttpForbiddenFactory;
use Laminas\Mvc\I18n\Router\TranslatorAwareTreeRouteStack;

return [
    'base_dir' => dirname(__DIR__, 3),
    'router' => [
        'router_class' => TranslatorAwareTreeRouteStack::class,
        'routes' => [
            'home' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'forbidden' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/forbidden',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'forbidden',
                    ],
                ],
            ],
            'site' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/site[/:action]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'contact' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/site/contact[/:action]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'contact',
                    ],
                ],
            ],
            'app.admin' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/cms/admin[/:action]',
                    'defaults' => [
                        'controller' => Controller\AdminController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'app.admin.add.setting' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/cms/admin/addsetting',
                    'defaults' => [
                        'controller' => Controller\AdminController::class,
                        'action'     => 'addsetting',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class,
            Controller\AdminController::class => InvokableFactory::class,
        ],
    ],
    'translator' => [
        'locale' => [
            //'en_US'
        ],
        'translation_file_patterns' => [
            [
                'type'     => Laminas\I18n\Translator\Loader\PhpArray::class,
                'filename' => 'en-US.php', 
                'base_dir' => __DIR__ . '/languages',
                'pattern'  => '%s.php',
            ],
//             [
//                 'type'        => Laminas\I18n\Translator\Loader\PhpArray::class,
//                 'base_dir'    => __DIR__ . '/languages',
//                 'pattern'     => 'user-%s.php',
//                 'text_domain' => 'user',
//             ],
            
        ],
        'translation_files' => [
            [
                'type' => 'PhpArray',
                'filename' => dirname(__DIR__, 3) . '/languages/en-US.php',
                'locale' => 'en-US',
                'text_domain' => 'default',
            ],
//             [
//                 'type' => 'PhpArray',
//                 'filename' => dirname(__DIR__, 3) . '/languages/user-en-US.php',
//                 'text_domain' => 'user',
//                 'locale' => 'en-US',
//             ],
            [
                'type' => 'PhpArray',
                'filename' => dirname(__DIR__, 3) . '/languages/es-MX.php',
                'text_domain' => 'default',
                'locale' => 'es-MX',
            ],
//             [
//                 'type' => 'PhpArray',
//                 'filename' => dirname(__DIR__, 3) . '/languages/user-es-MX.php',
//                 'text_domain' => 'user',
//                 'locale' => 'es-MX',
//             ],
        ],
    ],
    'navigation' => [
        'static' =>[
            [
                'label' => 'Home',
                'route' => 'home',
                'class' => 'nav-link',
                'order' => '-10',
            ],
            [
                'label' => 'Contact Us',
                'route' => 'contact',
                'class' => 'nav-link',
                'order' => '20',
            ],
            [
                'label' => 'Admin',
                'route' => 'app.admin',
                'class' => 'nav-link',
                'resource' => 'admin',
                'privilege' => 'admin.access',
            ],
        ],
        'admin' => [
            [
                'label' => 'Home',
                'route' => 'home',
                'class' => 'nav-link',
                'order' => '-10',
            ],
            [
                'label' => 'App Settings',
                'route' => 'app.admin',
                'class' => 'nav-link',
                'resource' => 'admin',
                'privilege' => 'admin.access',
            ],
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
    	'display_forbidden_notice' => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
    	'forbidden_template'       => 'error/403',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
             __DIR__ . '/../view',
        ],
    ],
];
