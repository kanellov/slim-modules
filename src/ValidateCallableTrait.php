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
use InvalidArgumentException;

/**
 * Provides method to validate if callable or in Container
 */
trait ValidateCallableTrait
{

    /**
     * If argument is not callable nor exists in container
     * thorws InvalidArgumentException
     * 
     * @param mixed $callable
     * @throws InvalidArgumentException
     */
    private function validateCallable($callable)
    {
        if ((is_string($callable) && $this->getContainer()->has($callable)) ||
            is_callable($callable)) {
            return;
        }

        throw new InvalidArgumentException('Cannot resolve callable');
    }

    /**
     * Abstract method that when implemented must return a Container
     * 
     * @return ContainerInterface
     */
    abstract protected function getContainer();
}
