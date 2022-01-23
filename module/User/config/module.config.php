<?php
namespace User;

use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;
use User\Controller\PasswordController;
use User\Controller\Service\PasswordControllerFactory;

return [
    'router' => [
        'routes' => [
            'user' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/user[/:action[/:userName]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'userName' => '[a-zA-Z][a-zA-Z0-9_-]*'
                    ],
                    'defaults' => [
                        'controller' => Controller\UserController::class,
                        'action' => 'index'
                    ]
                ]
            ],
            'profile' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/user/profile[/:action[/:userName]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'userName' => '[a-zA-Z][a-zA-Z0-9_-]*'
                    ],
                    'defaults' => [
                        'controller' => Controller\ProfileController::class,
                        'action' => 'view'
                    ]
                ]
            ],
            'password' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/user/password[/:action[/:step]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'step' => '[a-zA-Z][a-zA-Z0-9_-]*'
                    ],
                    'defaults' => [
                        'controller' => Controller\PasswordController::class,
                        'action' => 'index'
                    ]
                ]
            ],
            'user.register' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/user/register[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+'
                    ],
                    'defaults' => [
                        'controller' => Controller\RegisterController::class,
                        'action' => 'index'
                    ]
                ]
            ],
            'user.verify' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/user/register/verify',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+'
                    ],
                    'defaults' => [
                        'controller' => Controller\RegisterController::class,
                        'action' => 'verify'
                    ]
                ]
            ],
            'user.admin' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/user/admin[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+'
                    ],
                    'defaults' => [
                        'controller' => Controller\AdminController::class,
                        'action' => 'index'
                    ]
                ]
            ]
        ]
    ],
    'controllers' => [
        'factories' => [
            Controller\ErrorController::class => InvokableFactory::class,
            Controller\PasswordController::class => Controller\Service\PasswordControllerFactory::class
        ]
    ],
    'navigation' => [
        'static' => [
            [
                'label' => 'Users',
                'route' => 'user',
                'class' => 'nav-link',
                'resource' => 'user',
                'privilege' => 'user.view.list'
            ],
            [
                'label' => 'Profile',
                'route' => 'profile',
                'class' => 'nav-link',
                'action' => 'view',
                'resource' => 'user',
                'privilege' => 'view'
            ],
            [
                'label' => 'Login',
                'route' => 'user',
                'class' => 'nav-link',
                'action' => 'login',
                'resource' => 'user',
                'privilege' => 'login.view'
            ],
            [
                'label' => 'Logout',
                'route' => 'user',
                'class' => 'nav-link',
                'action' => 'logout',
                'resource' => 'user',
                'privilege' => 'logout'
            ],
            [
                'label' => 'Register',
                'route' => 'user.register',
                'class' => 'nav-link',
                'action' => 'index',
                'resource' => 'user',
                'privilege' => 'register.view'
            ]
        ],
        'admin' => [
            [
                'label' => 'Admin Users',
                'route' => 'user.admin',
                'class' => 'nav-link',
                // 'controller' => 'admin',
                'action' => 'index',
                'resource' => 'admin',
                'privilege' => 'admin.access'
            ],
            [
                'label' => 'Logout',
                'route' => 'user',
                'class' => 'nav-link',
                'action' => 'logout',
                'resource' => 'user',
                'privilege' => 'logout',
                'order' => 100
            ]
        ]
    ],
    'view_manager' => [
        'template_path_stack' => [
            'user' => __DIR__ . '/../view'
        ]
    ]
];