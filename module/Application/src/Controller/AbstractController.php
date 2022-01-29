<?php
namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\MvcEvent;
use Laminas\View\Model\ViewModel;
use Laminas\Authentication\AuthenticationService as AuthService;
use User\Model\UserTable as Table;
use User\Model\User as User;
use User\Model\Guest;
use Laminas\Log\Logger as Logger;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;

abstract class AbstractController extends AbstractActionController
{
    /**
     * @var $fm \Laminas\Mvc\Plugin\FlashMessenger
     */
    public $fm;
    /**
     * 
     * @var \Laminas\ServiceManager\ServiceManager $sm
     */
    public $sm;
    /**
     *
     * @var \Application\Utilities\Debug $debug
     */
    public $debug;
    public $baseUrl;
    public $basePath;
    /**
     *
     * @var string $referringUrl
     */
    public $referringUrl;
    /**
     *
     * @var $authService \Laminas\Authentication\AuthenticationService
     */
    public $authService;
    /**
     * @var User\Model\User $user
     */
    public $user;
    /**
     *
     * @var \Laminas\Log\Logger $logger
     */
    public $logger;
    /**
     *
     * @var \Laminas\View\ViewModel $view
     */
    public $view;
    /**
     *
     * @var User\Model\UserTable $table
     */
    public $table;
    /**
     *
     * @var \Laminas\Permission\Acl $acl
     */
    public $acl;

    public $authenticated = false;
    /**
     *
     * @var $config array
     */
    public $config;
    /**
     *
     * @var \Laminas\Config\Config $appSettings
     */
    public $appSettings;

    protected $action;

    protected $sessionContainer;

    public function onDispatch(MvcEvent $e)
    {
        // Get an instance of the Service Manager
        $this->sm = $e->getApplication()->getServiceManager();
        $this->debug = $this->sm->get('Application\Utilities\Debug');
        // Request Object
        $request = $this->sm->get('Request');
        // The Referring Url for the current request ie the previous page
        $this->referringUrl = $request->getServer()->get('HTTP_REFERER');
        // The Logger Service
        $this->logger = $this->sm->get('Laminas\Log\Logger');
        // Not sure why we need this....
        $this->baseUrl = $this->request->getBasePath();
        $this->basePath = dirname(__DIR__, 4);
        // The authentication Object
        $this->authService = new AuthService();
        // This removes the need for more than one db query to make settings available to Aurora
        $this->appSettings = $this->sm->get('AuroraSettings');
        // This may be removed in next branch
        $pluginManager = $this->sm->get('ControllerPluginManager');
        $this->config = $this->sm->get('config');
        // An instance of User\Model\User
        $table = $this->sm->get('User\Model\UserTable');
        // An instance of the Acl Service
        $this->acl = $this->sm->get('Acl');

        //var_dump($this->baseDir);
        /**
         *
         * @var $view \Laminas\View\Model\ViewModel
         */
        $this->view = new ViewModel();
        // Is the User Authenticated?
        switch ($this->authService->hasIdentity()) {
            case true :
                $this->authenticated = true;
                $this->user = $table->fetchByColumn('userName', $this->authService->getIdentity());
                break;
            default;
                $user = new Guest();
                $this->user = $user;
                break;
        }
        $this->view->setVariables([
        		'appSettings' => $this->appSettings,
        		'user' => $this->user,
        		'acl' => $this->acl,
        		'debug' => $this->debug
        ]);
        $this->layout()->appSettings = $this->appSettings;
        $this->action = $this->params()->fromRoute('action');
        $this->layout()->acl = $this->acl;
        $this->layout()->user = $this->user;
        $this->layout()->authenticated = $this->authenticated;
        $this->_init();
        
        return parent::onDispatch($e);
    }

    public function _init()
    {
        return $this;
    }
}