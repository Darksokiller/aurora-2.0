<?php
namespace Application\Listener;

use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Filter\FilterChain;
use Laminas\Filter\FilterInterface;
use Laminas\Filter\StringToLower;
use Laminas\Filter\Word\CamelCaseToDash;
use Laminas\Mvc\MvcEvent;
use Laminas\View\Resolver\PrefixPathStackResolver;
use Laminas\View\Resolver\TemplateMapResolver;
use Laminas\View\Resolver\TemplatePathStack;

class SkinListener extends AbstractListenerAggregate
{
    /** @var TemplateMapResolver */
    private $templateMapResolver;
    /** @var TemplatePathStack */
    private $templatePathStack;
    /** @var PrefixPathStackResolver */
    private $prefixPathStackResolver;
    /** @var FilterInterface */
    private $filter;

    public function __construct(
        TemplateMapResolver $templateMapResolver, 
        TemplatePathStack $templatePathStack,
        PrefixPathStackResolver $prefixPathStackResolver
        )
    {
        $this->templateMapResolver = $templateMapResolver;
        $this->templatePathStack = $templatePathStack;
        $this->prefixPathStackResolver = $prefixPathStackResolver;
        $this->filter              = (new FilterChain())
            ->attach(new CamelCaseToDash())
            ->attach(new StringToLower());
    }

    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(
            MvcEvent::EVENT_RENDER,
            [$this, 'setSkin']
        );
    }

    public function setSkin(MvcEvent $event) : void
    {
        // Get and check the route match object
        $routeMatch = $event->getRouteMatch();
        if (! $routeMatch) {
            return;
        }

        // Get and check the parameter for current controller FQN
        $controller = $routeMatch->getParam('controller');
        if (! $controller) {
            return;
        }
        $action = $routeMatch->getParam('action');
        $parts = explode('\\', $controller);
        $nameSpace = $parts[0];
        $string = $this->filter->filter($parts[2]); 
        $parts = explode('-', $string);
        $controller = $parts[0];
        $module = $this->filter->filter($nameSpace);
        // Get root view model
        $layoutViewModel = $event->getViewModel();

        // Rendering without layout?
        if ($layoutViewModel->terminate()) {
            return;
        }

        if($controller === 'admin')
        {
            $layoutName = 'layout/' . $controller;
            $layoutViewModel->setTemplate($layoutName);
        }
        
    }
}