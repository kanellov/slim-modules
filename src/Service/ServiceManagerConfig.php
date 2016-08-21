<?php

/**
 * kanellov/slim-modules
 * 
 * @link https://github.com/kanellov/slim-modules for the canonical source repository
 * @copyright Copyright (c) 2016 Vassilis Kanellopoulos (http://kanellov.com)
 * @license GNU GPLv3 http://www.gnu.org/licenses/gpl-3.0-standalone.html
 */

namespace Knlv\Slim\Modules\Service;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Handlers\NotAllowed;
use Slim\Handlers\NotFound;
use Slim\Handlers\Strategies\RequestResponse;
use Slim\Interfaces\Http\EnvironmentInterface;
use Slim\Interfaces\RouterInterface;
use Slim\Router;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\SharedEventManager;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\ArrayUtils;
use Zend\Stdlib\ResponseInterface;

class ServiceManagerConfig extends Config
{
    protected $config = [
        'abstract_factories' => [],
        'aliases'            => [
            'EventManagerInterface'            => 'events',
            EventManagerInterface::class       => 'events',
            'SharedEventManagerInterface'      => 'sharedEvents',
            SharedEventManagerInterface::class => 'sharedEvents',
            'ServiceLocatorInterface'          => 'services',
            ServiceLocatorInterface::class     => 'services',
            'ContainerInterface'               => 'services',
            ContainerInterface::class          => 'services',
            'ServiceManager'                   => 'services',
            ServiceManager::class              => 'services',
            RouterInterface::class             => 'router',
            EnvironmentInterface::class        => 'environment',
            ServerRequestInterface::class      => 'request',
            ResponseInterface::class           => 'response',
            'EventManager'                     => 'events',
        ],
        'delegators' => [],
        'factories'  => [
            'settings'         => SettingsFactory::class,
            'events'           => EventsFactory::class,
            'environment'      => EnvironmentFactory::class,
            'request'          => RequestFactory::class,
            'response'         => ResponseFactory::class,
            'errorHandler'     => ErrorHandlerFactory::class,
            'callableResolver' => CallableResolverFactory::class,
            'application'      => SlimAppFactory::class,
            'modules'          => ModulesFactory::class,
        ],
        'lazy_services' => [],
        'initializers'  => [],
        'invokables'    => [],
        'services'      => [],
        'shared'        => [
            'events' => false,
        ],
    ];

    public function __construct(array $config = [])
    {
        $this->config['factories']['services'] = function ($container) {
            return $container;
        };
        $this->config['factories']['sharedEvents'] = function () {
            return new SharedEventManager();
        };
        $this->config['factories']['router'] = function () {
            return new Router();
        };
        $this->config['factories']['foundHandler'] = function () {
            return new RequestResponse();
        };
        $this->config['factories']['notFoundHandler'] = function () {
            return new NotFound();
        };
        $this->config['factories']['notAllowedHandler'] = function () {
            return new NotAllowed();
        };
        $this->config['initializers']['EventManagerAwareInitialized'] = function ($first, $second) {
                if ($first instanceof ContainerInterface) {
                    $container = $first;
                    $instance  = $second;
                } else {
                    $container = $second;
                    $instance  = $first;
                }

                if (! $instance instanceof EventManagerAwareInterface) {
                    return;
                }

                $eventManager = $instance->getEventManager();

                if ($eventManager instanceof EventManagerInterface
                    && $eventManager->getSharedManager() instanceof SharedEventManagerInterface
                ) {
                    return;
                }

                $instance->setEventManager($container->get('events'));
            };

        $this->factories['services'] = function (ServiceLocatorInterface $serviceLocator) {
            return $serviceLocator;
        };

        if (method_exists($this, 'getAllowOverride')) {
            $config = ArrayUtils::merge($this->config, $config);
        }

        parent::__construct($config);
    }
}
