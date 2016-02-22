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
use Slim\App;

class SlimAppFactory
{
    public function __invoke(ContainerInterface $sm)
    {
        $config = $sm->get('config');

        $app = new App($sm);

        // set routes
        $routesCfg = isset($config['routes']) ? $config['routes'] : [];
        $addRoutes = new \Knlv\Slim\Modules\Utils\AddRoutes();
        $addRoutes($app, $routesCfg);

        // set middleware
        $middlewareCfg = isset($config['middleware']) ? $config['middleware'] : [];
        $addMiddleware = new \Knlv\Slim\Modules\Utils\AddMiddleware();
        $addMiddleware($app, $middlewareCfg);

        return $app;
    }
}
