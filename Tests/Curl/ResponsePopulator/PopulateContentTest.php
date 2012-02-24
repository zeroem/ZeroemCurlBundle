<?php

namespace Zeroem\RemoteHttpKernelBundle\Tests\Curl\ResponsePopulator;

use Zeroem\RemoteHttpKernelBundle\ResponsePopulator\Curl\PopulateContent;
use Symfony\Component\HttpFoundation\Response;

class PopulatContentTest extends \PHPUnit_Framework_TestCase
{
    const THE_BODY="This is a body...";

    public function testResponseContentGetsPopulated() {
        $response = new Response();
        $populator = new PopulateContent($response);

        $populator->populate(null,self::THE_BODY);

        $this->assertEquals(self::THE_BODY,$response->getContent());
    }
}