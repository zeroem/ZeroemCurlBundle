<?php

/*
 * (c) Darrell Hamilton <darrell.noice@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zeroem\CurlBundle\Curl;

/**
 * An OO Representation of the data returned by curl_multi_info_read
 */
class MultiInfo
{
    /**
     * @var resource the handle associated with this information
     */
    private $handle;
    private $message;
    private $result;

    public function __construct(array $info) {
        $this->handle = $info["handle"];
        $this->message = $info["msg"];
        $this->result = $info["result"];
    }


    public function getHandle() {
        return $this->handle;
    }

    public function getMessage() {
        return $this->message;
    }

    public function getResult() {
        return $this->result;
    }
}