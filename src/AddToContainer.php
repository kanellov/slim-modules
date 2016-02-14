<?php
/**
 * kanellov/slim-modules
 * 
 * @link https://github.com/kanellov/slim-modules for the canonical source repository
 * @copyright Copyright (c) 2016 Vassilis Kanellopoulos (http://kanellov.com)
 * @license GNU GPLv3 http://www.gnu.org/licenses/gpl-3.0-standalone.html
 */

namespace Knlv\Slim\Modules;

use Slim\App;
use Slim\Container;

class AddToContainer
{
    public function __invoke(App $app, array $config)
    {
        if (empty($config)) {
            return $app;
        }

        $container = $app->getContainer();

        if (isset($config['services']) && is_array($config['services'])) {
            $this->addServices($container, $config['services']);
        }

        if (isset($config['invokables']) && is_array($config['invokables'])) {
            $this->addInvokables($container, $config['invokables']);
        }

        if (isset($config['factories']) && is_array($config['factories'])) {
            $this->addFactories($container, $config['factories']);
        }

        if (isset($config['protected']) && is_array($config['protected'])) {
            $this->addProtected($container, $config['protected']);
        }

        if (isset($config['extensions']) && is_array($config['extensions'])) {
            $this->addExtensions($container, $config['extensions']);
        }

        return $app;
    }

    private function addServices(Container $container, array $services)
    {
        foreach ($services as $name => $service) {
            if (is_string($service) && class_exists($service)) {
                $service = new $service();
            }
            $container[$name] = $service;
        }
    }

    private function addInvokables(Container $container, array $invokables)
    {
        foreach ($invokables as $name => $invokable) {
            $container[$name] = function () use ($invokable) {
                return new $invokable();
            };
        }
    }

    private function addFactories(Container $container, array $factories)
    {
        foreach ($factories as $name => $factory) {
            if (is_string($factory) && class_exists($factory)) {
                $factory = new $factory();
            }
            $container[$name] = $container->factory($factory);
        }
    }

    private function addProtected(Container $container, array $protected)
    {
        foreach ($protected as $name => $callable) {
            if (is_string($callable) && class_exists($callable)) {
                $callable = new $callable();
            }
            $container[$name] = $container->protect($callable);
        }
    }

    private function addExtensions(Container $container, array $extensions)
    {
        foreach ($extensions as $name => $extensionGroup) {
            if (!is_array($extensionGroup)) {
                $extensionGroup = [$extensionGroup];
            }
            foreach ($extensionGroup as $extension) {
                if (is_string($extension) && class_exists($extension)) {
                    $extension = new $extension();
                }
                $container->extend($name, $extension);
            }
        }
    }
}
