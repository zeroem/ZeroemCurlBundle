<?php

namespace Zeroem\CurlBundle\Curl\Collector;

class ContentCollector implements CollectorInterface
{
    private $content = "";

    public function collect() {
        list($handle, $content) = func_get_args();

        // If the response is chunked, we only get parts at a time, so the
        // content must be appended to any existing content
        $this->content .= $content;

        return strlen($content);
    }

    public function retrieve() {
        return $this->content;
    }
}