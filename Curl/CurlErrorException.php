<?php

/*
 * (c) Darrell Hamilton <darrell.noice@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zeroem\CurlBundle\Curl;

class CurlErrorException extends \RuntimeException
{
    public function __construct($message="", $code=0, \Exception $previous=null) {
        parent::__construct($message,$code,$previous);
    }
}