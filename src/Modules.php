<?php
/**
 * kanellov/slim-modules
 * 
 * @link https://github.com/kanellov/slim-modules for the canonical source repository
 * @copyright Copyright (c) 2016 Vassilis Kanellopoulos (http://kanellov.com)
 * @license GNU GPLv3 http://www.gnu.org/licenses/gpl-3.0-standalone.html
 */

namespace Knlv\Slim\Modules;

use Zend\EventManager\EventManagerInterface;

class Modules
{
    /**
     *
     * @var array
     */
    protected $modules = [];
    
    /**
     * 
     * @var EventManagerInterface
     */
    protected $events;
    
    public function __construct(array $modules, EventManagerInterface $events)
    {
        $this->modules = $modules;
        $this->events = $events;
    }
}
