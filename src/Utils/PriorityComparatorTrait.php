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
 * Provides method to order array elements using priority comparator
 */
trait PriorityComparatorTrait
{
    /**
     * @var callable
     */
    private $comparator;

    /**
     * Orders array according priority field. The normal operation
     * is to order in descending. If $reversed is true the ordering
     * in ascending
     * 
     * @param array $config
     * @param bool $reversed
     * @return array
     */
    private function prioritize(array $config, $reversed = false)
    {
        if (null === $this->comparator) {
            $this->comparator = new PriorityComparator();
        }

        uasort($config, $this->comparator);

        if ($reversed) {
            return array_reverse($config, true);
        }

        return $config;
    }
}
