<?php

/*
 * (c) Darrell Hamilton <darrell.noice@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Curl\ResponsePopulator;

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

        $version_parts = explode("/",$version);
        $this->response->setProtocolVersion(array_pop($version_parts));
    }

    private function parseHeader($header) {
        if(!empty($header)) {
            list($name, $value) = explode(": ",$header);
            $this->response->headers->set($name, $value);
        }
    }
}