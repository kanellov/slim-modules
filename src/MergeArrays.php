<?php
/**
 * kanellov/slim-modules
 * 
 * @link https://github.com/kanellov/slim-modules for the canonical source repository
 * @copyright Copyright (c) 2016 Vassilis Kanellopoulos (http://kanellov.com)
 * @license GNU GPLv3 http://www.gnu.org/licenses/gpl-3.0-standalone.html
 */

namespace Knlv\Slim\Modules;

/**
 * Merges the values of the $new array into $merged array
 * 
 * @param array $merged
 * @param array $new
 * @return array
 */
class MergeArrays
{
    public function __invoke(array $merged, array $new)
    {
        foreach ($new as $key => $value) {
            if (array_key_exists($key, $merged)) {
                if (is_int($key)) {
                    $merged[] = $value;
                } elseif (is_array($value) && is_array($merged[$key])) {
                    $merged[$key] = $this($merged[$key], $value);
                } else {
                    $merged[$key] = $value;
                }
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }
}
