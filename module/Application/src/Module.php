<?php

declare(strict_types=1);

namespace Application;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Adapter\Adapter as dbAdapter;
use Laminas\Session;
use Laminas\Mvc\MvcEvent;
use Laminas\Session\SessionManager;
use Laminas\Session\Config\SessionConfig;
use Laminas\Session\Container;
use Laminas\Session\Validator;
use Application\Model\SettingsTable;
use Laminas\Mvc\Application;
use Laminas\Db\TableGateway\TableGateway;
use Laminas\Db\TableGateway\Feature\RowGatewayFeature;
use Laminas\Session\SaveHandler\DbTableGateway;
use Laminas\Session\SaveHandler\DbTableGatewayOptions;
use Application\Utilities\Mailer;
use Application\Utilities\Debug;
use Laminas\Log\Logger;
use Laminas\Log\Filter\Priority;
use Laminas\Log\Writer\Db;
use Laminas\Log\Writer\FirePhp;
use Laminas\Log\Formatter\Db as DbFormatter;
use Laminas\Log\Formatter\FirePhp as FireBugformatter;
use Application\Listener\SkinListener;
use Laminas\View\Resolver\TemplateMapResolver;
use Laminas\Config\Config;
use Laminas\ModuleManager\Feature\ViewHelperProviderInterface;

class Module implements ViewHelperProviderInterface
{
    public function getConfig(): array
    {
        /** @var array $config */
        $config = include __DIR__ . '/../config/module.config.php';
        return $config;
    }
    public function onBootstrap(MvcEvent $e)
    {
        $this->bootstrapSettings($e);
        $this->bootstrapSession($e);
        $this->bootstrapLogging($e);
        $this->boostrapTranslation($e);
        $config = $e->getApplication()->getServiceManager()->get('config');
        $viewConfig = $config['view_manager'];
        $skinName = 'default';
        // $base_template_path_stack = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'skin'. DIRECTORY_SEPARATOR .'view' . DIRECTORY_SEPARATOR . $skinName;
        // $viewConfig['template_path_stack'] = $base_template_path_stack . DIRECTORY_SEPARATOR . 'module';
        // $viewConfig['template_map']['layout/layout'] = $base_template_path_stack . DIRECTORY_SEPARATOR . 'layout' . DIRECTORY_SEPARATOR . 'layout.phtml';
        // $viewConfig['template_map']['application/index/index'] = $viewConfig['template_path_stack'] . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'index' . DIRECTORY_SEPARATOR . 'index.phtml';
        // $viewConfig['template_map']['error/403'] = $viewConfig['template_path_stack'] . DIRECTORY_SEPARATOR . 'error' . DIRECTORY_SEPARATOR . '403.phtml';
        // $viewConfig['template_map']['error/404'] = $viewConfig['template_path_stack'] . DIRECTORY_SEPARATOR . 'error' . DIRECTORY_SEPARATOR . '404.phtml';
        // $viewConfig['template_map']['error/index'] = $viewConfig['template_path_stack'] . DIRECTORY_SEPARATOR . 'error' . DIRECTORY_SEPARATOR . 'index.phtml';
        $exit = '';
        $application = $e->getApplication();
        $sm = $e->getApplication()->getServiceManager();

        /** @var TemplateMapResolver $templateMapResolver */
        $templateMapResolver = $sm->get('ViewTemplateMapResolver');
        $templateStackResolver = $sm->get('ViewTemplatePathStack');
        $prefixPathStackResolver = $sm->get('ViewPrefixPathStackResolver');

        // Create and register Skin listener
        $listener = new SkinListener($templateMapResolver, $templateStackResolver, $prefixPathStackResolver);
        $listener->attach($application->getEventManager());
    }
    public function bootstrapSettings($e)
    {
        $sm = $e->getApplication()->getServiceManager();
        $settings = $sm->get('Application\Model\SettingsTableGateway');
        $handler = new Config($settings->fetchall());
        $sm->setService('AuroraSettings', $handler);
        date_default_timezone_set($handler->timeZone);
    }
    /**
     * 
     * @param $e \Laminas\Mvc\MvcEvent
     */
    public function boostrapTranslation($e)
    {
        // get an instance of the service manager
        $sm = $e->getApplication()->getServiceManager();
        $settings = $sm->get('AuroraSettings');
        if($settings->enableTranslation) {
       // var_dump($sm->get('router'));
        /**
         * 
         * @var $request \Laminas\Http\PhpEnvironment\Request
         */
        $request = $sm->get('request');
        // get the laguages sent by the client 
        $string = $request->getServer('HTTP_ACCEPT_LANGUAGE');
        //var_dump($string);
        // this should be delimeter for the first two prefrences set in the browser
        $needle = ';';
        // find its position
        $position = strpos($string, $needle);
        // return everything before the needle
        $substring = substr($string, 0, $position);
        // get an array of locales with the primary at offest 0
        $locales = explode(',', $substring);        
        /**
         * 
         * @var $translator \Laminas\I18n\Translator\Translator
         */
        $translator = $sm->get('MvcTranslator');
        // set the primary locale as requested by the client
        $translator->setLocale($locales[0]);
        // set option two as the fallback
        $translator->setFallbackLocale([$locales[1]]);
        /**
         * 
         * @var $renderer \Laminas\View\Renderer\PhpRenderer
         */
        $renderer = $sm->get('ViewRenderer');
        // attach the Il8n standard helpers for translation
        $renderer->getHelperPluginManager()->configure(
            (new \Laminas\I18n\ConfigProvider())->getViewHelperConfig()
            );
        }
    }
    /*
     * @var $e Laminas\\Mvc\Event
     */
    public function bootstrapLogging($e)
    {
        $sm = $e->getapplication()->getServiceManager();
        $settings = $sm->get('AuroraSettings');
        $config = $sm->get('config');
        $logger = $sm->get('Laminas\Log\Logger');
        $writer = new Db(new dbAdapter($config['db']), 'log');
        $standardLogFilter = new Priority(Logger::DEBUG);
        $writer->addFilter($standardLogFilter);

        if($settings->firebugDebug)
        {
            $firePhpWriter = new FirePhp();
            $debugFilter = new Priority(Logger::DEBUG);
            $firePhpWriter->addFilter($debugFilter);
            $writer->addFilter($debugFilter);
            $logger->addWriter($firePhpWriter);
        }
        
        $dbFormatter = new DbFormatter();
        $dbFormatter->setDateTimeFormat($settings->timeFormat);
        $writer->setFormatter($dbFormatter);
        $logger->addWriter($writer);
        if($settings->enableErrorLogging) {
            Logger::registerErrorHandler($logger);
        }
    }
    public function bootstrapSession($e)
    {
        try {
            $serviceManager = $e->getApplication()->getServiceManager();
            $session = $serviceManager->get(SessionManager::class);
            $tableGateway = new TableGateway('session', $serviceManager->get(AdapterInterface::class));
            $saveHandler  = new DbTableGateway($tableGateway, new DbTableGatewayOptions());
            $session->setSaveHandler($saveHandler);
            $session->rememberMe();
            $session->start();
            $container = new Session\Container('initialized');
            //new session creation
            $request = $serviceManager->get('Request');
            $session->regenerateId(true);
            $container->init = 1;
            $container->remoteAddr = $request->getServer()->get('REMOTE_ADDR');
            $container->httpUserAgent = $request->getServer()->get('HTTP_USER_AGENT');
            $config = $serviceManager->get('Config');
            $sessionConfig = $config['session'];
            $chain = $session->getValidatorChain();
            
            foreach ($sessionConfig['validators'] as $validator) {
                switch ($validator) {
                    case Validator\HttpUserAgent::class:
                        $validator = new $validator($container->httpUserAgent);
                        break;
                    case Validator\RemoteAddr::class:
                        $validator = new $validator($container->remoteAddr);
                        break;
                    default:
                        $validator = new $validator();
                }
                $chain->attach('session.validate', array($validator, 'isValid'));
            }
        } catch (\Laminas\Session\Exception\RuntimeException $e) {
            //session has expired
            return;
        }
        //let's check if our session is not already created (for the guest or user)
        if (isset($container->init)) {
            return;
        }
    }
    public function getServiceConfig()
    {
        return [
            'factories' => [
                // SessionManager::class => function ($container) {
                //     $config = $container->get('session_config');
                //     $session = $config['session'];
                //     $sessionConfig = new $session['session_config']['class']();
                //    // $sessionConfig->setOptions($session['session_config']['options']);
                //     $sessionManager = new Session\SessionManager($sessionConfig,
                //         new $session['storage'](),
                //         null
                //         );
                //     \Laminas\Session\Container::setDefaultManager($sessionManager);
                    
                //     return $sessionManager;
                // },
                Model\SettingsTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    return new SettingsTable(new TableGateway('settings', $dbAdapter, new RowGatewayFeature('id')));
                },
                Utilities\Mailer::class => function($container) {
                    $settings = $container->get('AuroraSettings');
                    $request = $container->get('request');
                    return new Mailer($settings, $request, $container);
                },
                Utilities\Debug::class => function($container) {
                	return new Debug();
                },
            ],
            
        ];
    }
    public function getViewHelperConfig()
    {
    	return [
    			'aliases' => [
    					'iconifiedcontrol' => View\Helper\IconifiedControl::class,
    					'IconifiedControl' => View\Helper\IconifiedControl::class,
    					'iconifiedControl' => View\Helper\IconifiedControl::class,
    			],
    			'factories' => [
    					View\Helper\IconifiedControl::class => View\Helper\Service\IconifiedControlFactory::class,
    			],
    	];
    }
}