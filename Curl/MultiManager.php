<?php

/*
 * (c) Darrell Hamilton <darrell.noice@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zeroem\CurlBundle\Curl;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Manage the execution of multiple Curl\Request objects in parallel
 */
class MultiManager implements CurlRequest
{

    private static $errors = array(
        CURLM_BAD_HANDLE      => 'Bad Handle',
        CURLM_BAD_EASY_HANDLE => 'Bad Easy Handle',
        CURLM_OUT_OF_MEMORY   => 'Out of Memory',
        CURLM_INTERNAL_ERROR  => 'Internal Error'
    );

    /**
     * The cURL multi handle
     *
     * @var resource
     */
    private $_handle;
    
    /**
     * A hash of object ids and the associated Request object registered with this object
     *
     * @var array
     */
    private $requests = array();

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * Whether we should block until finished, let it go and finish processing
     * via the destructor
     *
     * @var boolean
     */
    private $blocking;

    public function __construct(EventDispatcherInterface $dispatcher=null,$blocking=true) {
        $this->dispatcher = $dispatcher;
        $this->_handle = curl_multi_init();
        $this->blocking = $blocking;
    }

    public function __destruct() {
        if(!$this->blocking) {
            $this->executeBlocking();
        }

        foreach($this->requests as $request) {
            $this->removeRequest($request);
        }

        curl_multi_close($this->_handle);
    }

    /**
     * Add a cURL request to be processed in parallel
     *
     * Alias of the curl_multi_add_handle function
     *
     * @param Request $request A Request object to be executed
     * @return MultiManager $this
     */
    public function addRequest(Request $request) {
        $oid = spl_object_hash($request);

        if(!isset($this->requests[$oid])) {
            $this->requests[$oid] = $request;
            curl_multi_add_handle($this->_handle, $request->getHandle());
        }
        
        return $this;
    }

    /**
     * Remove a request from the execution stack
     *
     * Analogous to the curl_multi_remove_handle function
     *
     * @param Request $request The Request to be removed
     * @return Request|boolean the request that was removed or false if the request isn't managed by this object
     */
    public function removeRequest(Request $request) {
        $oid = spl_object_hash($request);
        $result = false;

        if(isset($this->requests[$oid])) {
            unset($this->requests[$oid]);
            $result = $request;
            curl_multi_remove_handle($this->_handle, $request->getHandle());
        }

        return $result;
    }

    /**
     * Get the content returned by a Request managed by this object
     *
     * Analogous to curl_multi_getcontent
     *
     * @param Request $request The Request to get the results for
     * @return string
     */
    public function getContent(Request $request) {
        return curl_multi_getcontent($request->getHandle());
    }

    /**
     * Execute the registered Request objects in parallel
     *
     * Analogous to curl_multi_exec
     * 
     * @return int the result of curl_multi_exec
     * @throws CurlErrorException 
     */
    public function execute() {
        if($this->blocking) {
            return $this->executeBlocking();
        } else {
            return $this->errorCheck(
                curl_multi_exec($this->_handle,$active)
            );
        }
    }

    private function executeBlocking() {
        $info = false;
        $active = false;
        $status = false;
        
        do {
            $status = curl_multi_exec($this->_handle, $active);

            // Block until there's something to do.
            if(curl_multi_select($this->_handle) != -1) {
                $this->processMultiInfo();
            }
        } while ($status === CURLM_CALL_MULTI_PERFORM || $active);

        
        // We shouldn't do any processing if the multi handle blows up
        $this->errorCheck($status);

        // Finish processing any remaining request data
        $this->processMultiInfo();

        return $status;
    }

    private function errorCheck($status) {
        if(isset(static::$errors[$status])) {
            throw new CurlErrorException(static::$errors[$status]);
        }

        return $status;
    }

    private function processMultiInfo() {
        while($info = curl_multi_info_read($this->_handle)) {
            $this->processInfo($info);
        } 
    }

    /**
     * Process and callbacks associated with the handle returned by
     * curl_multi_info_read
     *
     * @param array $info data array from curl_multi_info_read
     */
    private function processInfo(array $info) {
        $request = $this->findRequest($info["handle"]);

        if(isset($this->dispatcher) && false !== $request) {
            $this->dispatcher->dispatch(
                CurlEvents::MULTI_INFO, 
                new MultiInfoEvent(
                    $this, 
                    $request, 
                    new MultiInfo($info)
                )
            );
        }
    }

    /**
     * Given a cURL handle, locate the request object associated with it
     *
     * @param resource $handle a cURL handle
     * @return Request|boolean the associated Request object or false if it is not found
     */
    public function findRequest($handle) {
        foreach($this->requests as $request) {
            if($handle === $request->getHandle()) {
                return $request;
            }
        }

        return false;
    }
}
