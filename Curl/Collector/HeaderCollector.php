<?php

namespace Zeroem\CurlBundle\Curl\Collector;

use Symfony\Component\HttpFoundation\Cookie;

class HeaderCollector implements CollectorInterface
{
    private $headers = array();
    private $version;
    private $code;
    private $message;

    public function collect() {
        list($handle, $headerString) = func_get_args();

        $cleanHeader = trim($headerString);

        // The HTTP/1.0 200 OK header is also passed through this function
        // and must be parsed differently than the other HTTP headers
        if(false !== stripos($cleanHeader,"http/")) {
            $this->parseHttp($cleanHeader);
        } else {
            $this->parseHeader($cleanHeader);
        }
        
        return strlen($headerString);
    }

    /**
     * Parse the `HTTP/1.0 200 OK' header into the proper
     * Status Code/Message and Protocol Version fields
     *
     * @param string $header
     */
    private function parseHttp($header) {
        list($version,$code,$message) = explode(" ", $header);
        
        $versionParts = explode("/",$version);
        $this->version = end($versionParts);
        $this->code = $code;
        $this->message = $message;
    }

    /**
     * Parse the standard `Header-name: value' headers into
     * individual header name/value pairs
     *
     * @param string $header
     */
    private function parseHeader($header) {
        if(!empty($header)) {
            $pos = strpos($header, ": ");

            if(false !== $pos) {
                $name = substr($header,0,$pos);
                $value = substr($header,$pos+2);

                $this->headers[$name] = $value;
            }
        }
    }

    public function retrieve() {
        return $this->headers;
    }

    public function getVersion() {
        return $this->version;
    }

    public function getMessage() {
        return $this->message;
    }

    public function getCode() {
        return $this->code;
    }
}
