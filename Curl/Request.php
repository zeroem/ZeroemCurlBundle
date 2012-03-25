<?php

/*
 * (c) Darrell Hamilton <darrell.noice@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zeroem\CurlBundle\Curl;

/**
 * An OO wrapper on the curl_* functions in PHP
 */
class Request implements CurlRequest
{
    /**
     * the cURL handle resource for this request
     * @var resource
     */
    private $handle;

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


    /**
     * Instantiate a new cURL Request object
     *
     * @param $url string URL to initialize the cURL handle with
     */
    public function __construct($url=null) {
        if(isset($url)) {
            $this->handle = curl_init($url);
        } else {
            $this->handle = curl_init();
        }
    }

    /**
     * Getter for the internal curl handle resource
     *
     * @return resource the curl handle
     */
    public function getHandle() {
        return $this->handle;
    }

    /**
     * Alias of the curl_setopt function
     */
    public function setOption($option, $value) {
        if(CurlOptions::checkOptionValue($option,$value)) {
            return curl_setopt($this->handle, $option, $value);
        }
    }

    /**
     * Alias of the curl_setopt_array function
     */
    public function setOptionArray(array $arr) {
        foreach($arr as $option => $value) {
            CurlOptions::checkOptionValue($option, $value);
        }

        return curl_setopt_array($this->handle, $arr);
    }

    public function __destruct() {
        curl_close($this->handle);
    }

    /**
     * Execute the cURL request
     *
     * @return mixed the results of curl_exec
     * @throws CurlErrorException
     */
    public function execute() {
        $value = curl_exec($this->handle);

        $error_no = curl_errno($this->handle);

        if (0 !== $error_no) {
            throw new CurlErrorException(curl_error($this->handle), $error_no);
        }

        return $value;
    }

    /**
     * Alias of the curl_getinfo function
     */
    public function getInfo($flag=null) {
        if(isset($flag)) {
            return curl_getinfo($this->handle,$flag);
        } else {
            return curl_getinfo($this->handle);
        }
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
            return $this->setOption(static::$_methodOptionMap[$method],true);
        } else {
            return $this->setOption(CURLOPT_CUSTOMREQUEST,$method);
        }
    }

    public function __clone() {
        $this->handle = curl_copy_handle($this->handle);
    }
}

