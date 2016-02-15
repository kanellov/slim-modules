<?php
/**
 * kanellov/slim-modules
 * 
 * @link https://github.com/kanellov/slim-modules for the canonical source repository
 * @copyright Copyright (c) 2016 Vassilis Kanellopoulos (http://kanellov.com)
 * @license GNU GPLv3 http://www.gnu.org/licenses/gpl-3.0-standalone.html
 */

namespace Knlv\Slim\Modules;

use Composer\Autoload\ClassLoader;
use Slim\App;

class AppLoader
{
    private $app;

    public function __construct(App $app, ClassLoader $autoloader, array $appConfig)
    {
        $events                  = 'Knlv\\events';
        $this->app               = $app;
        $container               = $app->getContainer();
        $container['autoloader'] = $autoloader;
        $container['events']     = $events;
        $this->loadAppConfig($appConfig);
        $results                         = $events('trigger', 'app.config', $this->app);
        $container['settings']['config'] = array_reduce($results['results'], function ($merged, $config) {
            if (!is_array($config)) {
                return;
            }

            return $this->mergeArrays($config, $merged);
        }, $container['settings']['config']);
    }

    private function loadAppConfig(array $appConfig)
    {
        $container   = $this->app->getContainer();
        $cacheConfig = isset($appConfig['config_cache']) ? $appConfig['config_cache'] : false;

        if (!isset($appConfig['modules']) || !is_array($appConfig['modules'])) {
            $appConfig['modules'] = [];
        }

        if ($cacheConfig && is_readable($cacheConfig)) {
            $this->loadModules($appConfig['modules']);
            $config = include $cacheConfig;
        } else {
            $config['settings'] = array_reduce(
                glob($appConfig['config_glob_path'], GLOB_BRACE),
                function ($config, $file) {
                    return $this->mergeArrays($config, include $file);
                },
                []
            );

            $config = $this->mergeArrays(
                $config,
                $this->loadModules($appConfig['modules'], true)
            );

            if ($cacheConfig && is_writable(dirname($cacheConfig))) {
                file_put_contents(
                    $cacheConfig,
                    '<?php return ' . var_export($config, true) . ';'
                );
            }
        }
        $container['settings']['appConfig'] = $appConfig;
        $container['settings']['config']    = $config;
    }

    public function init()
    {
        $container = $this->app->getContainer();
        $config    = $container['settings']['config'];

        $settings      = isset($config['settings']) ? $config['settings'] : [];
        $toContainer   = isset($config['container']) ? $config['container'] : [];
        $routes        = isset($config['routes']) ? $config['routes'] : [];
        $middleware    = isset($config['middleware']) ? $config['middleware'] : [];

        $addSettings      = new AddSettings();
        $addToContainer   = new AddToContainer();
        $addRoutes        = new AddRoutes();
        $addMiddleware    = new AddMiddleware();

        $addMiddleware(
            $addRoutes(
               $addToContainer(
                    $addSettings($this->app, $settings),
                    $toContainer
                ),
                $routes
            ),
            $middleware
        );
        $events    = $container['events'];
        $events('trigger', 'app.bootstrap', $this->app);

        return $this->app;
    }

    private function loadModules(array $modules, $mergeConfigs = false)
    {
        $config = [];
        foreach ($modules as $module) {
            if (!is_readable($module)) {
                throw new Exception('Module is not readable');
            }

            $moduleCallable = include $module;
            if (!is_callable($moduleCallable)) {
                throw new \Exception('No valid module');
            }

            $moduleConfig = call_user_func($moduleCallable, $this->app);

            if ($mergeConfigs && is_array($moduleConfig)) {
                $config = $this->mergeArrays($config, $moduleConfig);
            }
        }

        return $config;
    }

    private function mergeArrays()
    {
        static $mergeArrays;
        if (null === $mergeArrays) {
            $mergeArrays = new MergeArrays();
        }

        return call_user_func_array($mergeArrays, func_get_args());
    }
}
