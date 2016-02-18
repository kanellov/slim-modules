<?php
/**
 * kanellov/slim-modules
 * 
 * @link https://github.com/kanellov/slim-modules for the canonical source repository
 * @copyright Copyright (c) 2016 Vassilis Kanellopoulos (http://kanellov.com)
 * @license GNU GPLv3 http://www.gnu.org/licenses/gpl-3.0-standalone.html
 */

namespace Knlv\Slim\Modules;

use Zend\ServiceManager\ServiceManager;

class Application
{
    public static function init(array $configuration = [])
    {
        $servicesCfg = isset($configuration['container']) ? $configuration['container'] : [];
        $services    = new ServiceManager(new Service\ServiceManagerConfig($servicesCfg));
        $services->setService('AppConfig', $configuration);
    }
}
