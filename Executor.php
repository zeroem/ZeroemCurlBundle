<?php

namespace Zeroem\RequestExecutorBundle;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Utility class for parsing a Request object into a cURL request
 * and executing it.
 *
 * @author Darrell Hamilton <darrell.noice@gmail.com>
 */
class Executor
{
  static private $_methodOptionMap = array(
    "GET"=>CURLOPT_GET,
    "POST"=>CURLOPT_POST,
    "HEAD"=>CURLOPT_NOBODY,
    "PUT"=>CURLOPT_PUT
  );

  /**
   * Execute a Request object via cURL
   *
   * @param Request $request the request to execute
   * @param array $options additional curl options to set/override
   * @return Response
   */
  static public function execute(Request $request, array $options = array()) {

    $handle = curl_init($request->getUri());

    curl_setopt($handle,CURLOPT_RETURNTRANSFER,true);

    static::setMethod($handle,$request);

    if ("POST" === $request->getMethod()) {
      static::setPostFields($handle, $request);
    }

    // Provided Options should override interpreted options
    curl_setopt_array($handle, $options);

    $result = curl_exec($handle);
    curl_close($handle);
  }

  static private function setPostFields(resource $handle, Request $request) {
    $postfields = null;
    $content = $request->getContent();

    if (null !== $content) {
      $postfields = $content;
    } else if (count($request->getRequest()->all()) > 0) {
      $postfields = $request->getRequest()->all();
    }

    curl_setopt($handle, CURLOPT_POSTFIELDS, $postfields);
  }

  static private function setMethod(resource $handle, Request $request) {
    $method = $request->getMethod();

    if(isset(static::$_methodOptionMap[$method])) {
      curl_setopt($handle,static::$_methodOptionMap[$method],true);
    } else {
      curl_setopt($handle,CURLOPT_CUSTOMREQUEST,$method);
    }
  }

  static private errorCheckAndHandling(resource $handle) {
      $error_no = curl_errno($handle);

      if(0 !== $error_no) {
          throw new CurlErrorException(curl_error($handle), $error_no);
      }
  }
  
}