<?php

namespace Zeroem\RemoteHttpKernelBundle\Tests;

use Zeroem\RemoteHttpKernelBundle\RemoteHttpKernel;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class RemoteHttpKernelTest extends \PHPUnit_Framework_TestCase
{

    public function testHandleRequest() {
        $request = Request::create("http://www.wikipedia.org");
        $kernel = new RemoteHttpKernel();

        $response = $kernel->handle($request);

        $this->assertEquals(200,$response->getStatusCode());
    }
}