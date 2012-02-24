<?php

namespace Zeroem\RemoteHttpKernelBundle\Curl\ResponsePopulator;

use Symfony\Component\HttpFoundation\Response;

class PopulateHeader extends AbstractPopulator
{
    public function populate() {
        list($handle, $headerString) = func_get_args();

        $cleanHeader = trim($headerString);


        if(false !== stripos($cleanHeader,"http/")) {
            $this->parseHttp($cleanHeader);
        } else {
            $this->parseHeader($cleanHeader);
        }
        
        return strlen($headerString);
    }

    private function parseHttp($header) {
        list($version,$code,$message) = explode(" ", $header);

        $this->response->setStatusCode($code,$message);
        $this->response->setProtocolVersion(array_pop(explode("/",$version)));
    }

    private function parseHeader($header) {
        if(!empty($header)) {
            list($name, $value) = explode(": ",$header);
            $this->response->headers->set($name, $value);
        }
    }
}