<?php

/*
 * (c) Darrell Hamilton <darrell.noice@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zeroem\CurlBundle\Tests\Curl;

use Zeroem\CurlBundle\Curl\RequestGenerator;
use Zeroem\CurlBundle\Curl\Request;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RequestGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testSetAndUnsetOptions() {
        $generator = new RequestGenerator();

        $this->assertInstanceOf(
            "\Zeroem\CurlBundle\Curl\Request",
            $generator->getRequest()
        );

        $request = new Request();
        $generator = new RequestGenerator($request);

        $this->assertInstanceOf(
            "\Zeroem\CurlBundle\Curl\Request",
            $generator->getRequest()
        );

        $this->assertNotEquals(
            $request,
            $generator->getRequest()
        );
    }
}