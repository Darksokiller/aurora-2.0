<?php
namespace User\Controller\Service;

use Application\Utilities\Debug;
use Interop\Container\ContainerInterface;
use User\Controller\PasswordController;

class PasswordControllerFactory
{

    public function __invoke(ContainerInterface $container)
    {
        $userTable = $container->get('User\Model\UserTable');
        // Debug::dump($userTable, __FILE__ . '::' . __LINE__ . '$userTable');
        return new PasswordController($userTable);
    }
}