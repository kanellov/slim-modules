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
use Slim\Http\Headers;
use Slim\Http\Response;

class ResponseFactory
{
    public function __invoke(ContainerInterface $sm)
    {
        $headers = new Headers([
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
        $response = new Response(200, $headers);

        return $response->withProtocolVersion(
            $sm->get('settings')['httpVersion']
        );
    }
}
