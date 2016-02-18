<?php

/**
 * kanellov/slim-modules
 * 
 * @link https://github.com/kanellov/slim-modules for the canonical source repository
 * @copyright Copyright (c) 2016 Vassilis Kanellopoulos (http://kanellov.com)
 * @license GNU GPLv3 http://www.gnu.org/licenses/gpl-3.0-standalone.html
 */

namespace Knlv\Slim\Modules\Service;

use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;

class ServiceManagerConfig extends Config
{
    /**
     * Services that can be instantiated without factories
     *
     * @var array
     */
    protected $invokables = [
        'sharedEvents'      => 'Zend\EventManager\SharedEventManager',
        'router'            => 'Slim\Router',
        'foundHandler'      => 'Slim\Handlers\Strategies\RequestResponse',
        'notFoundHandler'   => 'Slim\Handlers\NotFound',
        'notAllowedHandler' => 'Slim\Handlers\NotAllowed',
    ];
    /**
     * Service factories
     *
     * @var array
     */
    protected $factories = [
        'settings'         => 'Knlv\Slim\Modules\Service\SettingsFactory',
        'events'           => 'Knlv\Slim\Modules\Service\EventsFactory',
        'environment'      => 'Knlv\Slim\Modules\Service\EnvironmentFactory',
        'request'          => 'Knlv\Slim\Modules\Service\RequestFactory',
        'response'         => 'Knlv\Slim\Modules\Service\ResponseFactory',
        'errorHandler'     => 'Knlv\Slim\Modules\Service\ErrorHandlerFactory',
        'callableResolver' => 'Knlv\Slim\Modules\Service\CallableResolverFactory',
    ];
    /**
     * Abstract factories
     *
     * @var array
     */
    protected $abstractFactories = [];
    /**
     * Aliases
     *
     * @var array
     */
    protected $aliases = [
        'Zend\EventManager\EventManagerInterface'     => 'events',
        'Zend\ServiceManager\ServiceLocatorInterface' => 'services',
        'Zend\ServiceManager\ServiceManager'          => 'services',
        'Slim\Interfaces\RouterInterface'             => 'router',
        'Slim\Router'                                 => 'router',
        'Slim\Interfaces\Http\EnvironmentInterface'   => 'environment',
        'Slim\Http\Environment'                       => 'environment',
        'Psr\Http\Message\ServerRequestInterface'     => 'request',
        'Slim\Http\Request'                           => 'request',
        'Psr\Http\Message\ResponseInterface'          => 'response',
        'Slim\Http\Response'                          => 'response',
    ];
    /**
     * Shared services
     *
     * Services are shared by default; this is primarily to indicate services
     * that should NOT be shared
     *
     * @var array
     */
    protected $shared = [
        'events' => false,
    ];
    /**
     * Delegators
     *
     * @var array
     */
    protected $delegators = [];
    /**
     * Initializers
     *
     * @var array
     */
    protected $initializers = [];

    public function __construct(array $configuration = [])
    {
        $this->initializers = [
            'EventManagerAwareInitializer' => function ($instance, ServiceLocatorInterface $services) {
                if ($instance instanceof EventManagerAwareInterface) {
                    $events = $instance->getEventManager();
                    if ($events instanceof EventManagerInterface) {
                        $events->setSharedManager($services->get('SharedEventManager'));
                    } else {
                        $instance->setEventManager($services->get('EventManager'));
                    }
                }
            },
            'ServiceManagerAwareInitializer' => function ($instance, ServiceLocatorInterface $services) {
                if ($services instanceof ServiceManager && $instance instanceof ServiceManagerAwareInterface) {
                    $instance->setServiceManager($services);
                }
            },
            'ServiceLocatorAwareInitializer' => function ($instance, ServiceLocatorInterface $services) {
                if ($instance instanceof ServiceLocatorAwareInterface) {
                    $instance->setServiceLocator($services);
                }
            },
        ];

        $this->factories['services'] = function (ServiceLocatorInterface $serviceLocator) {
            return $serviceLocator;
        };

        parent::__construct(ArrayUtils::merge(
            [
                'invokables'         => $this->invokables,
                'factories'          => $this->factories,
                'abstract_factories' => $this->abstractFactories,
                'aliases'            => $this->aliases,
                'shared'             => $this->shared,
                'delegators'         => $this->delegators,
                'initializers'       => $this->initializers,
            ],
            $configuration
        ));
    }
}
