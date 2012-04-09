<?php

/*
 * (c) Darrell Hamilton <darrell.noice@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zeroem\CurlBundle\Tests\Curl\ResponsePopulator;

use Zeroem\CurlBundle\Curl\Collector\ContentCollector;

class PopulatContentTest extends \PHPUnit_Framework_TestCase
{
    const THE_BODY="This is a body...";

    public function testResponseContentGetsPopulated() {
        $collector = new ContentCollector();

        $collector->collect(null,self::THE_BODY);

        $this->assertEquals(self::THE_BODY,$collector->retrieve());
    }
}