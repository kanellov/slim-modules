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
use Slim\Collection;
use Zend\Stdlib\ArrayUtils;

class SettingsFactory
{
    /**
     * Default settings
     *
     * @var array
     */
    private static $defaultSettings = [
        'httpVersion'                       => '1.1',
        'responseChunkSize'                 => 4096,
        'outputBuffering'                   => 'append',
        'determineRouteBeforeAppMiddleware' => false,
        'displayErrorDetails'               => false,
    ];

    public function __invoke(ContainerInterface $sm)
    {
        $config   = $sm->get('config');
        $settings = isset($config['settings']) ? $config['settings'] : [];

        return new Collection(
            ArrayUtils::merge(self::$defaultSettings, $settings)
        );
    }
}
