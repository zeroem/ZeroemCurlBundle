<?php

/*
 * (c) Darrell Hamilton <darrell.noice@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zeroem\CurlBundle\Tests\HttpKernel;

use Zeroem\CurlBundle\HttpKernel\RemoteHttpKernel;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class RemoteHttpKernelTest extends \PHPUnit_Framework_TestCase
{

    public function testHandleRequest() {
        $request = Request::create("http://symfony.com");
        $kernel = new RemoteHttpKernel();

        $response = $kernel->handle($request);

        $this->assertEquals(200,$response->getStatusCode());
    }
}
