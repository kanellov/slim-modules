<?php
/**
 * kanellov/slim-modules
 * 
 * @link https://github.com/kanellov/slim-modules for the canonical source repository
 * @copyright Copyright (c) 2016 Vassilis Kanellopoulos (http://kanellov.com)
 * @license GNU GPLv3 http://www.gnu.org/licenses/gpl-3.0-standalone.html
 */

namespace Knlv\Slim\Modules;

use Interop\Container\ContainerInterface;
use RuntimeException;
use Zend\Stdlib\ArrayUtils;

class Modules
{
    /**
     *
     * @var array
     */
    private $modules = [];

    /**
     *
     * @var bool
     */
    private $modulesAreLoaded = false;

    /**
     *
     * @var array
     */
    private $megedConfig = [];

    /**
     *
     * @var ContainerInterface
     */
    private $services;

    public function __construct(array $modules, ContainerInterface $services)
    {
        $this->modules  = $modules;
        $this->services = $services;
    }

    public function loadModules()
    {
        if (true === $this->modulesAreLoaded) {
            return $this->megedConfig;
        }

        $this->megedConfig = array_reduce($this->modules, function ($config, $module) {
            return ArrayUtils::merge($config, $this->loadModule($module));
        }, []);

        $this->modulesAreLoaded = true;

        return $this->megedConfig;
    }

    private function loadModule($module)
    {
        if (!is_readable($module)) {
            throw new RuntimeException(sprintf(
                'Cannot read file %s to load module',
                 $module
            ));
        }

        $moduleCallable = include $module;

        if (is_string($moduleCallable) && class_exists($moduleCallable)) {
            $module = new $moduleCallable();
        }

        if (!is_callable($moduleCallable)) {
            throw new RuntimeException(sprintf(
                'File %s does not return callable',
                $module
            ));
        }
        $moduleConfig = call_user_func($moduleCallable, $this->services);

        return is_array($moduleConfig) ? $moduleConfig : [];
    }
}
