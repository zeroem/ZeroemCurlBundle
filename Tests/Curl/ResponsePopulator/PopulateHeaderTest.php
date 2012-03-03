<?php

/*
 * (c) Darrell Hamilton <darrell.noice@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zeroem\CurlBundle\Tests\Curl\ResponsePopulator;

use Zeroem\CurlBundle\Curl\ResponsePopulator\PopulateHeader;
use Symfony\Component\HttpFoundation\Response;

class PopulatHeaderTest extends \PHPUnit_Framework_TestCase
{
    public function testPopulatesVersionStatusAndMessage() {
        $response = new Response();
        $populator = new PopulateHeader($response);

        $populator->populate(null,"HTTP/1.1 200 OK\r\n");

        $this->assertEquals("1.1",$response->getProtocolVersion());
        $this->assertEquals(200, $response->getStatusCode());
        
        // The Response object currently doesn't have an easy way to check
        // the status text
        //$this->assertEquals("OK", $response->getStatusText());
    }

    public function testPopulateStandardHeaders() {
        $response = new Response();
        $populator = new PopulateHeader($response);

        $populator->populate(null,"Content-type: text/html\r\n");
        $populator->populate(null,"X-forwarded-for: 127.0.0.1\r\n");

        $this->assertEquals("text/html", $response->headers->get("content-type"));
        $this->assertEquals("127.0.0.1", $response->headers->get("x-forwarded-for"));
    }
}