<?php

/*
 * (c) Darrell Hamilton <darrell.noice@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zeroem\CurlBundle\HttpKernel;

use Symfony\Component\HttpKernel\HttpKernelInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\HeaderBag;

use Zeroem\CurlBundle\Curl\Request as CurlRequest;
use Zeroem\CurlBundle\Curl\ResponsePopulator\PopulateHeader;
use Zeroem\CurlBundle\Curl\ResponsePopulator\PopulateContent;
use Zeroem\CurlBundle\Curl\CurlErrorException;


/**
 * RemoteHttpKernel utilizes curl to convert a Request object into a Response
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


    /**
     * Handles a Request to convert it to a Response.
     *
     * When $catch is true, the implementation must catch all exceptions
     * and do its best to convert them to a Response instance.
     *
     * @param  Request $request A Request instance
     * @param  integer $type    The type of the request
     *                          (one of HttpKernelInterface::MASTER_REQUEST or HttpKernelInterface::SUB_REQUEST)
     * @param  Boolean $catch   Whether to catch exceptions or not
     *
     * @return Response A Response instance
     *
     * @throws \Exception When an Exception occurs during processing
     */
    public function handle(Request $request, $type = HttpKernelInterface::SUB_REQUEST, $catch = true) {
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
     *
     * @return Response
     *
     * @throws CurlErrorException 
     */
    private function handleRaw(Request $request, array $options = array()) {
        $handle = curl_init($request->getUri());
        $curl = new CurlRequest($request->getUri());
        $response = new Response();

        $curl->setOption(CURLOPT_HTTPHEADER,$this->buildHeadersArray($request->headers));

        $curl->setMethod($request->getMethod());

        if ("POST" === $request->getMethod()) {
            $this->setPostFields($curl, $request);
        }

        if("PUT" === $request->getMethod() && count($request->files->all()) > 0) {
            $file = current($request->files->all());

            $curl->setOptionArray(
                array(
                    CURLOPT_INFILE=>'@'.$file->getRealPath(),
                    CURLOPT_INFILESIZE=>$file->getSize()
                )
            );
        }

        $curl->setOptionArray($options);

        // These options must not be tampered with to ensure proper functionality
        $curl->setOptionArray(
            array(
                CURLOPT_HEADERFUNCTION=>array(new PopulateHeader($response),"populate"),
                CURLOPT_WRITEFUNCTION=>array(new PopulateContent($response),"populate"),
            )
        );

        $curl->execute();

        return $response;
    }


    /**
     * Populate the POSTFIELDS option
     *
     * @param resource $handle the curl handle
     * @param Request $request the Request object we're populating
     */
    private function setPostFields(CurlRequest $curl, Request $request) {
        $postfields = null;
        $content = $request->getContent();

        if (null !== $content) {
            $postfields = $content;
        } else if (count($request->getRequest()->all()) > 0) {
            $postfields = $request->getRequest()->all();
        }

        $curl->setOption(CURLOPT_POSTFIELDS, $postfields);
    }

    /**
     * Convert a HeaderBag into an array of headers appropriate for cURL
     *
     * @param HeaderBag $headerBag headers to parse
     *
     * @return array An array of header strings
     */
    private function buildHeadersArray(HeaderBag $headerBag) {
        return explode("\r\n",$headerBag);
    }
}
