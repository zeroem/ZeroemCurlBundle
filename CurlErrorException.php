<?php

namespace Zeroem\RequestExecutorBundle;

class CurlErrorException extends \RuntimeException
{
    public function __construct($message="", $code=0, \Exception $previous=null) {
        parent::__construct($message,$code,$previous);
    }
}