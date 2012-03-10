<?php

/*
 * (c) Darrell Hamilton <darrell.noice@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zeroem\CurlBundle\Tests\Curl;

use Zeroem\CurlBundle\Curl\Request;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    public function testClone() {
        $request = new Request();
        $clone = clone $request;

        $this->assertThat(
            $request->getHandle(),
            $this->logicalNot(
                $this->equalTo($clone->getHandle())
            )
        );
    }
}

