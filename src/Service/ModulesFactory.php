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
use Knlv\Slim\Modules\Modules;

class ModulesFactory
{
    public function __invoke(ContainerInterface $sm)
    {
        $appConfig = $sm->get('appConfig');

        return new Modules($appConfig['modules'], $sm);
    }
}
