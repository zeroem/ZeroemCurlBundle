<?php

/*
 * (c) Darrell Hamilton <darrell.noice@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zeroem\CurlBundle\Tests\Curl\ResponsePopulator;

use Zeroem\CurlBundle\Curl\Collector\HeaderCollector;

class HeaderCollectorTest extends \PHPUnit_Framework_TestCase
{
    public function testPopulatesVersionStatusAndMessage() {
        $collector = new HeaderCollector();
        $collector->collect(null,"HTTP/1.1 200 OK\r\n");
        $collector->collect(null,"Content-type: text/html\r\n");
        $collector->collect(null,"X-forwarded-for: 127.0.0.1\r\n");

        $this->assertArrayHasKey("Content-type", $collector->retrieve());
        $this->assertArrayHasKey("X-forwarded-for", $collector->retrieve());

        $headers = $collector->retrieve();
        $this->assertEquals("text/html", $headers["Content-type"]);
        $this->assertEquals("127.0.0.1", $headers["X-forwarded-for"]);

        $this->assertEquals("1.1",$collector->getVersion());
        $this->assertEquals(200, $collector->getCode());
        $this->assertEquals("OK", $collector->getMessage());
    }
}