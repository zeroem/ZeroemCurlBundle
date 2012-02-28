<?php

namespace Symfony\Component\Curl;

class Request
{
    private $_handle;

    /**
     * Map specific HTTP requests to their appropriate CURLOPT_* constant
     *
     * @var array
     */
    static private $_methodOptionMap = array(
        "GET"=>CURLOPT_HTTPGET,
        "POST"=>CURLOPT_POST,
        "HEAD"=>CURLOPT_NOBODY,
        "PUT"=>CURLOPT_PUT
    );


    public function __construct($url=null) {
        if(isset($url)) {
            $this->_handle = curl_init($url);
        } else {
            $this->_handle = curl_init();
        }
    }

    public function getHandle() {
        return $this->handle;
    }

    public function setOption($key, $value) {
        return curl_setopt($this->_handle, $key, $value);
    }

    public function setOptionArray(array $arr) {
        return curl_setopt_array($this->_handle, $arr);
    }

    public function __destruct() {
        curl_close($this->_handle);
    }

    public function execute() {
        $value = curl_exec($this->_handle);

        $error_no = curl_errno($this->_handle);

        if (0 !== $error_no) {
            throw new CurlErrorException(curl_error($this->_handle), $error_no);
        }

        return $value;
    }

    public function getInfo() {
        return curl_getinfo($this->_handle);
    }

    /**
     * Convenience method for setting the appropriate cURL options based on the desired
     * HTTP request method
     *
     * @param resource $handle the curl handle
     * @param Request $request the Request object we're populating
     */
    public function setMethod($method) {
        if (isset(static::$_methodOptionMap[$method])) {
            curl_setopt($this->_handle,static::$_methodOptionMap[$method],true);
        } else {
            curl_setopt($this->_handle,CURLOPT_CUSTOMREQUEST,$method);
        }
    }
}
