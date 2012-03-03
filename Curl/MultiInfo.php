<?php

namespace Zeroem\CurlBundle\Curl;


class MultiInfo
{
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