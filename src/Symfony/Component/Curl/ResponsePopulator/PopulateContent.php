<?php

/*
 * (c) Darrell Hamilton <darrell.noice@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Curl\ResponsePopulator;

use Symfony\Component\HttpFoundation\Response;

/**
 * Populates the content field of the Response object
 *
 * This is intended to be passed to cURL as the callback for
 * CURLOPT_WRITEFUNCTION.
 *
 * The function will be passed two arguments, the cURL handle 
 * responsible for the callback, and the content to be appended.
 *
 * The function must return the number of bytes written.
 */
class PopulateContent extends AbstractPopulator
{
    /**
     * @param resource $handle cURL handle
     * @param string $content partial or complete content of the cURL request
     *
     * @return integer length of the content written
     */
    public function populate() {
        list($handle, $content) = func_get_args();

        // If the response is chunked, we only get parts at a time, so the
        // content must be appended to any existing content
        $this->response->setContent($this->response->getContent().$content);

        return strlen($content);
    }
}

