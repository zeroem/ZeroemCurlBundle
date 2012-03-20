<?php

/*
 * (c) Darrell Hamilton <darrell.noice@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zeroem\CurlBundle\Curl;

/**
 * A service class for generating Curl\Request objects with an initial
 * set of CURLOPT_* options set
 */
class RequestGenerator
{
    private $request;

    public function __construct($arg=array()) {
        if(is_array($arg)) {
            $this->request = new Request();
            $this->request->setOptionArray($arg);
        } else if( $arg instanceof Request ) {
            $this->request = clone $arg;
        } else {
            if(is_object($arg)) {
                $type = get_class($arg);
            } else {
                $type = gettype($arg);
            }
            throw new \LogicException(
                "Unsupported argument type.  Expected array instance of Request. Got {$type}."
            );
        }
    }

    /**
     * Generate a Request object with preset options
     *
     * @return Request a cURL Request object
     */
    public function getRequest() {
        return clone $this->request;
    }
}

