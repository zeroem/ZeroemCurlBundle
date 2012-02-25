<?php

/*
 * (c) Darrell Hamilton <darrell.noice@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Curl\ResponsePopulator;

use Symfony\Component\HttpFoundation\Response;

abstract class AbstractPopulator
{
    protected $response;

    public function __construct(Response $response) {
        $this->response = $response;
    }

    abstract function populate();
}