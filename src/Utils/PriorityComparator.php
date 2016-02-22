<?php
/**
 * kanellov/slim-modules
 * 
 * @link https://github.com/kanellov/slim-modules for the canonical source repository
 * @copyright Copyright (c) 2016 Vassilis Kanellopoulos (http://kanellov.com)
 * @license GNU GPLv3 http://www.gnu.org/licenses/gpl-3.0-standalone.html
 */

namespace Knlv\Slim\Modules\Utils;

/**
 * Compares arrays based on the value of priority field
 */
class PriorityComparator
{
    /**
     * The comparison is descending
     * 
     * @param array $config1
     * @param array $config2
     * @return int
     */
    public function __invoke(array $config1, array $config2)
    {
        if (!isset($config1['priority'])) {
            $config1['priority'] = 1;
        }

        if (!isset($config2['priority'])) {
            $config2['priority'] = 1;
        }

        return $config1['priority'] <= $config2['priority'] ? 1 : -1;
    }
}
