<?php
/**
 * kanellov/slim-modules
 * 
 * @link https://github.com/kanellov/slim-modules for the canonical source repository
 * @copyright Copyright (c) 2016 Vassilis Kanellopoulos (http://kanellov.com)
 * @license GNU GPLv3 http://www.gnu.org/licenses/gpl-3.0-standalone.html
 */

namespace Knlv\Slim\Modules;

use Knlv\Slim\Modules\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\ArrayUtils;

class Application
{
    public static function init(array $configuration = [])
    {
        $servicesCfg = new ServiceManagerConfig(
            isset($configuration['container'])
                ? $configuration['container'] : []
        );
        $services = new ServiceManager($servicesCfg->toArray());
        /* @var $services \Zend\ServiceManager\ServiceManager */
        $services->setService('appConfig', $configuration);

        $events = $services->get('events');
        /* @var $events \Zend\EventManager\EventManagerInterface */
        $events->setIdentifiers([\Slim\App::class]);
        $services->setService('appEvents', $events);

        $cacheConfig = isset($configuration['config_cache'])
                ? $configuration['config_cache'] : false;

        $config = [];
        if ($cacheConfig && is_readable($cacheConfig)) {
            $config = include $cacheConfig;
            $services->get('modules')->loadModules();
        } else {
            $config['settings'] = self::loadSettings($configuration);
            $config             = ArrayUtils::merge(
                $config,
                $services->get('modules')->loadModules()
            );

            if ($cacheConfig && is_writable(dirname($cacheConfig))) {
                file_put_contents(
                    $cacheConfig,
                    '<?php ' . PHP_EOL . 'return ' . var_export($config, true) . ';'
                );
            }
        }

        $services->setService('config', $config);

        $result = $events->trigger('app.config', $services);
        /* @var $result \Zend\EventManager\ResponseCollection */

        $services->setAllowOverride(true);
        $services->setService('config', array_reduce(
            iterator_to_array($result),
            function ($config, $moduleCfg) {
                return ArrayUtils::merge($config, $moduleCfg);
            },
            $services->get('config')
        ));
        $services->setAllowOverride(false);

        $services->configure($config['container']);

        $app = $services->get('application');
        $events->trigger('app.bootstrap', $app);

        return $app;
    }

    private static function loadSettings($appConfig)
    {
        if (isset($appConfig['config_glob_path'])) {
            return array_reduce(
                glob($appConfig['config_glob_path'], GLOB_BRACE),
                function ($config, $file) {
                    return ArrayUtils::merge($config, include $file);
                },
                []
            );
        }

        return [];
    }
}
