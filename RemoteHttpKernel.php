<?php

namespace Zeroem\RemoteHttpKernelBundle;

use Symfony\Component\HttpKernel\HttpKernelInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\HeaderBag;

use Zeroem\RemoteHttpKernelBundle\Curl\ResponsePopulator\PopulateHeader;
use Zeroem\RemoteHttpKernelBundle\Curl\ResponsePopulator\PopulateContent;
use Zeroem\RemoteHttpKernelBundle\Curl\CurlErrorException;


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
        "GET"=>CURLOPT_HTTPGET,
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
        return new Response(
            $e->getMessage(),
            500
        );
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
                CURLOPT_HEADERFUNCTION=>array(new PopulateHeader($response),"populate"),
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
        
        if (false !== $exception) {
            throw $exception;
        }

        curl_close($handle);
        return $response;
    }

    private function setPostFields($handle, Request $request) {
        $postfields = null;
        $content = $request->getContent();

        if (null !== $content) {
            $postfields = $content;
        } else if (count($request->getRequest()->all()) > 0) {
            $postfields = $request->getRequest()->all();
        }

        curl_setopt($handle, CURLOPT_POSTFIELDS, $postfields);
    }

    private function setMethod($handle, Request $request) {
        $method = $request->getMethod();

        if (isset(static::$_methodOptionMap[$method])) {
            curl_setopt($handle,static::$_methodOptionMap[$method],true);
        } else {
            curl_setopt($handle,CURLOPT_CUSTOMREQUEST,$method);
        }
    }

    private function getErrorException($handle) {
        $error_no = curl_errno($handle);

        if (0 !== $error_no) {
            return new CurlErrorException(curl_error($handle), $error_no);
        }

        return false;
    }

    private function buildHeadersArray(HeaderBag $headerBag) {
        return explode("\r\n",$headerBag);
    }
}
