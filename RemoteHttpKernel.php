<?php

namespace Zeroem\RemoteHttpKernelBundle;

use Symfony\Component\HttpKernel\HttpKernelInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\HeaderBag;

use Zeroem\RemoteHttpKernelBundle\ResponsePopulator\PopulateHeaders;
use Zeroem\RemoteHttpKernelBundle\ResponsePopulator\PopulateContent;

/**
 * Utility class for parsing a Request object into a cURL request
 * and executing it.
 *
 * @author Darrell Hamilton <darrell.noice@gmail.com>
 */
class RemoteHttpKernel implements HttpKernelInterface
{
    /**
     * Additional Curl Options to override calculated values
     * and to set values that cannot be interpreted
     *
     * @var array
     */
    private $curlOptions;

    public function __construct(array $curlOptions = array()) {
        $this->curlOptions = $curlOptions;
    }

    static private $_methodOptionMap = array(
        "GET"=>CURLOPT_GET,
        "POST"=>CURLOPT_POST,
        "HEAD"=>CURLOPT_NOBODY,
        "PUT"=>CURLOPT_PUT
    );

    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true) {
        try {
            return $this->handleRaw($request, $this->curlOptions);
        } catch (\Exception $e) {
            if (false === $catch) {
                throw $e;
            }

            return $this->handleException($e, $request);
        }
    }

    private function handleException(\Exception $e, Request $request) {

    }

    /**
     * Execute a Request object via cURL
     *
     * @param Request $request the request to execute
     * @param array $options additional curl options to set/override
     * @return Response
     */
    private function handleRaw(Request $request, array $options = array()) {

        $handle = curl_init($request->getUri());

        $response = new Response();

        curl_setopt_array(
            $handle,
            array(
                CURLOPT_HTTPHEADER=>$this->buildHeadersArray($request->headers),
                CURLOPT_HEADERFUNCTION=>array(new PopulateHeaders($response),"populate"),
                CURLOPT_WRITEFUNCTION=>array(new PopulateContent($response),"populate"),
            )
        );

        $this->setMethod($handle,$request);

        if ("POST" === $request->getMethod()) {
            $this->setPostFields($handle, $request);
        }

        // Provided Options should override interpreted options
        curl_setopt_array($handle, $options);

        curl_exec($handle);

        $exception = $this->getErrorException($handle);
        curl_close($handle);
        
        if (false !== $exception) {
            throw $exception;
        }

        $response->setStatusCode(curl_getinfo($handle,CURLINFO_HTTP_CODE));

        return $response;
    }

    private function setPostFields(resource $handle, Request $request) {
        $postfields = null;
        $content = $request->getContent();

        if (null !== $content) {
            $postfields = $content;
        } else if (count($request->getRequest()->all()) > 0) {
            $postfields = $request->getRequest()->all();
        }

        curl_setopt($handle, CURLOPT_POSTFIELDS, $postfields);
    }

    private function setMethod(resource $handle, Request $request) {
        $method = $request->getMethod();

        if (isset(static::$_methodOptionMap[$method])) {
            curl_setopt($handle,static::$_methodOptionMap[$method],true);
        } else {
            curl_setopt($handle,CURLOPT_CUSTOMREQUEST,$method);
        }
    }

    private function getErrorException(resource $handle) {
        $error_no = curl_errno($handle);

        if (0 !== $error_no) {
            return new CurlErrorException(curl_error($handle), $error_no);
        }

        return false;
    }

    private function buildHeadersArray(HeaderBag $headerBag) {
        $headers = array();

        foreach($headerBag->all() as $header=>$value) {
            $headers[] = makeHeaderName($header) . ": {$value}";
        }

        return $headers;
    }

    private function makeHeaderName($str) {
        $parts = explode("_",strtolower($str));
        
        foreach($parts as &$part) {
            $part = ucfirst($part);
        }

        return implode("-",$parts);
    }
}
