<?php
namespace User;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\ModuleManager\Feature\ViewHelperProviderInterface;
use User\Model\User;
use User\Model\UserTable;
use User\Model\Profile;
use User\Model\ProfileTable;
use User\Model\RolesTable;
use Application\Db\RowGateway\RowGateway;
use Laminas\Permissions\Acl\Acl;
use User\Permissions\PermissionsManager;
use User\View\Service\UserAwareControlFactory;

class Module implements ConfigProviderInterface, ViewHelperProviderInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
    public function onBootstrap($e) {
        $this->bootstrapAcl($e);
    }
    public function bootstrapAcl($e)
    {
        $sm = $e->getApplication()->getServiceManager();
        $acl = new PermissionsManager(new Acl(), $sm->get('User\Model\RolesTable'));
        $acl = $acl->getAcl();
        
        $sm->setService('Acl', $acl);
    }
    public function getViewHelperConfig()
    {
    	return [
    			'aliases' => [
    					'userawarecontrol' => View\Helper\UserAwareControl::class,
    					'UserAwareControl' => View\Helper\UserAwareControl::class,
    					'userAwareControl' => View\Helper\UserAwareControl::class,
    					'userControl'      => View\Helper\UserAwareControl::class,
    			],
    			'factories' => [
    					View\Helper\UserAwareControl::class => View\Helper\Service\UserAwareControlFactory::class,
    			],
    	];
    }
    public function getServiceConfig()
    {
       
        return [
            'factories' => [
                Model\UserTable::class => function($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new User('id','user', $dbAdapter));
                    return new Model\UserTable('user', $dbAdapter, null, $resultSetPrototype);
                },
                Model\ProfileTable::class => function($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Profile('id', 'user_profile', $dbAdapter));
                    return new Model\ProfileTable('user_profile', $dbAdapter, null, $resultSetPrototype);
                },
                Model\RolesTable::class => function($container) {
                  $dbAdapter = $container->get(AdapterInterface::class);
                  $resultSetPrototype = new ResultSet();
                  $resultSetPrototype->setArrayObjectPrototype(new RowGateway('id', 'user_roles', $dbAdapter));
                  return new Model\RolesTable('user_roles', $dbAdapter, null, $resultSetPrototype);
                },
                ],
                ];
    }
    public function getControllerConfig()
    {
        return [
            'factories' => [
                Controller\UserController::class => function($container) {
                    return new Controller\UserController(
                        $container->get(Model\UserTable::class)
                        );
                },
                Controller\ProfileController::class => function($container) {
                    return new Controller\ProfileController(
                        $container->get(Model\UserTable::class)
                        );
                },
                Controller\RegisterController::class => function($container) {
                    return new Controller\RegisterController(
                        $container->get(Model\UserTable::class)
                        );
                },
                Controller\AdminController::class => function($container) {
                    return new Controller\AdminController(
                        $container->get(Model\UserTable::class)
                        );
                },
                ],
                ];
    }
    public function getFilterConfig()
    {
        return [
            'factories' => [
                Filter\PasswordFilter::class => function($container) {
                    return new Filter\PasswordFilter(
                        $container->get(Filter\PasswordFilter::class)
                        );
                },
                Filter\RoleFilter::class => function($container) {
                    return new Filter\RoleFilter(
                        $container->get(Filter\RoleFilter::class)
                    );
                },
                ],
                ];
    }
}