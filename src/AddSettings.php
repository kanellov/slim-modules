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

/**
 * Replaces settings in Slim\App container
 */
class AddSettings
{
    /**
     * Replaces settings of Slim\App in container
     * 
     * @param App $app
     * @param array $settings
     * @return App
     */
    public function __invoke(App $app, array $settings)
    {
        if (empty($settings)) {
            return $app;
        }

        $container = $app->getContainer();
        $container['settings']->replace($settings);

        return $app;
    }
}
