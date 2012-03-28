<?php

/*
 * (c) Darrell Hamilton <darrell.noice@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zeroem\CurlBundle\Curl\ResponsePopulator;

/**
 * Populates the headers of the Response object
 *
 * This is intended to be passed to cURL as the callback for
 * CURLOPT_HEADERFUNCTION.
 *
 * The function will be passed two arguments, the cURL handle 
 * responsible for the callback, and an HTTP header
 *
 * The function must return the number of bytes written.
 */
class PopulateHeader extends AbstractPopulator
{
    /**
     * @param resource $handle cURL handle
     * @param string $headerString one HTTP Header to be written
     *
     * @return integer length of the content written
     */
    public function populate() {
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

        $this->response->setStatusCode($code,$message);

        $version_parts = explode("/",$version);
        $this->response->setProtocolVersion(array_pop($version_parts));
    }

    /**
     * Parse the standard `Header-name: value' headers into
     * individual header name/value pairs
     *
     * @param string $header
     */
    private function parseHeader($header) {
        if(!empty($header) && strpos(": ", $header)) {
            list($name, $value) = explode(": ",$header);
            $this->response->headers->set($name, $value);
        }
    }
}
