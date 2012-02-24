<?php

namespace Zeroem\RemoteHttpKernelBundle\ResponsePopulator;

use Symfony\Component\HttpFoundation\Response;

class PopulateHeaders extends AbstractPopulator
{
    public function populate() {
        list($handle, $headerString) = func_get_args();

        $headers = explode("\n", $headerString);

        foreach($headers as $header) {
            list($name, $value) = explode(": ",$header);
            $this->response->headers->set($name, $value);
        }
        
        return strlen($headerString);
    }
}