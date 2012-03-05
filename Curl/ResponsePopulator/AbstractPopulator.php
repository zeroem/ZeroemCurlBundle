<?php

/*
 * (c) Darrell Hamilton <darrell.noice@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zeroem\CurlBundle\Curl\ResponsePopulator;

use Symfony\Component\HttpFoundation\Response;

/**
 * A Command Patter than encapsulates a Response object
 * to be acted upon.
 *
 * Intended to be passed as callback functions to cURL for
 * processing the results into a proper Response object
 */
abstract class AbstractPopulator
{
    /**
     * @var Response
     */
    protected $response;

    
    /**
     * @param Response $response
     */
    public function __construct(Response $response) {
        $this->response = $response;
    }

    /**
     * Takes an arbitrary list of arguments and applies it to the 
     * encapsulated Response object
     */
    abstract function populate();
}
