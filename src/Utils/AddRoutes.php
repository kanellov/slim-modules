<?php
/**
 * kanellov/slim-modules
 * 
 * @link https://github.com/kanellov/slim-modules for the canonical source repository
 * @copyright Copyright (c) 2016 Vassilis Kanellopoulos (http://kanellov.com)
 * @license GNU GPLv3 http://www.gnu.org/licenses/gpl-3.0-standalone.html
 */

namespace Knlv\Slim\Modules\Utils;

use InvalidArgumentException;
use Slim\App;

class AddRoutes
{
    use PriorityComparatorTrait;
    use ValidateCallableTrait;
    use AddMiddlewareTrait;

    private $defined = [];

    private $container;

    public function __invoke(App $app, array $routes)
    {
        if (empty($routes)) {
            return $app;
        }

        if (array_values($routes) === $routes) {
            throw new InvalidArgumentException('Routes array must have route names as keys');
        }

        $this->container = $app->getContainer();
        $routes          = $this->prioritize($routes);
        $that            = $this;

        foreach ($routes as $name => $config) {
            if (!isset($config['pattern'])) {
                throw new InvalidArgumentException('Route must have a pattern');
            }

            if (array_key_exists($config['pattern'], $this->defined)) {
                continue;
            }

            if (isset($config['children']) && is_array($config['children'])) {
                $route = $app->group($config['pattern'], function () use ($that, $app, $config) {
                    $that($app, $config['children']);
                });
            } else {
                $route = $this->createRoute($app, $config);
                $route->setName($name);
                $this->defined[$config['pattern']] = 1;
            }
            if (isset($config['middleware']) && is_array($config['middleware'])) {
                $this->addMiddleware($route, $config['middleware']);
            }
        }

        return $app;
    }

    private function createRoute(App $app, array $config)
    {
        if (!isset($config['handler'])) {
            throw new InvalidArgumentException('Route must have a handler');
        }

        $this->validateCallable($config['handler']);

        $pattern = $config['pattern'];
        $handler = $config['handler'];
        $methods = isset($config['methods']) ? array_unique($config['methods']) : ['GET'];
        $route   = $app->map($methods, $pattern, $handler);

        if (isset($config['output_buffering'])) {
            $route->setOutputBuffering($config['output_buffering']);
        }

        return $route;
    }

    private function getContainer()
    {
        return $this->container;
    }
}
