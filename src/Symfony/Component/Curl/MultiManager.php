<?php

namespace Symfony\Component\Curl;

/**
 * 
 */
class MultiManager implements CurlRequest
{
    /**
     * The cURL multi handle
     *
     * @var resource
     */
    private $handle;

    /**
     * A hash of object ids and the associated Request object registered with this object
     *
     * @var array
     */
    private $requests = array();

    /**
     * A hash of object ids and the array of callbacks associated with that object id
     *
     * @var array
     */
    private $callbacks = array();

    public function __construct() {
        $this->handle = curl_multi_init();
    }

    public function __destruct() {
        foreach($this->requests as $request) {
            $this->removeRequest($request);
        }

        curl_multi_close($this->handle);
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
            curl_multi_add_handle($request->getHandle());
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
            curl_multi_remove_handle($request->getHandle());
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
        return curl_multi_getcontent($this->handle, $request->getHandle());
    }

    /**
     * Execute the registered Request objects in parallel
     *
     * Analogous to curl_multi_exec
     * 
     * @return int the result of curl_multi_exec
     */
    public function execute() {
        $info = false;
        $active = false;
        $status = false;

        do {
            $status = curl_multi_exec($this->handle, $active);
            $info = curl_multi_info_read($this->handle);
            if (false !== $info) {
                $this->processInfo($info);
            }
        } while ($status === CURLM_CALL_MULTI_PERFORM || $active);

        while($info !== false) {
            $this->processInfo($info);
        }

        return $status;
    }

    /**
     * Process and callbacks associated with the handle returned by
     * curl_multi_info_read
     *
     * @param array $info data array from curl_multi_info_read
     */
    private function processInfo(array $info) {
        $request = $this->findRequest($info["handle"]);

        if($request !== false) {
            $this->executCallbacks($request, $info["msg"], $info["result"]);
        }
    }

    private function executeCallbacks(Request $request, $message, $result) {
        $oid = spl_object_hash($request);

        if(isset($this->callbacks[$oid])) {
            foreach($this->callbacks[$oid] as $callback) {
                $callback($request, $message, $result);
            }
        }
    }

    /**
     * Given a cURL handle, locate the request object associated with it
     *
     * @param resource $handle a cURL handle
     * @return Request|boolean the associated Request object or false if it is not found
     */
    private function findRequest($handle) {
        foreach($this->requests as $request) {
            if($handle === $request->getHandle()) {
                return $request;
            }

            return false;
        }
    }

    
    /**
     * Register a callback function to be called when curl_multi_get_info
     * returns information about this request during the execution process
     *
     * @param Request $request The request this callback will be bound to
     * @param callable $callback the function to be called.  Takes 3 parameters, the Request object and
     * the msg and result data returned from curl_multi_get_info
     *
     * @return MultiManager $this
     */
    public function addCallback(Request $request, $callback) {
        $oid = spl_object_hash($request);

        if(!isset($this->callbacks[$oid])) {
            $this->callbacks[$oid] = array();
        }

        $this->callbacks[$oid][] = $callback;

        return $this;
    }

    /**
     * Remove a callback associated with a particular Request object
     *
     * @param Request $request
     * @param callable $callback
     * @return callable|boolean the callback that was removed or false if it was not found
     */
    public function removeCallback(Request $request, $callback) {
        $oid = spl_object_hash($request);

        if(isset($this->callbacks[$oid])) {
            $index = array_search($callback, $this->callbacks[$oid], true);

            if($index !== false) {
                unset($this->callbacks[$oid][$index]);
                return $callback;
            }
        }

        return false;
    }
}
