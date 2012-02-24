<?php

namespace Zeroem\RemoteHttpKernelBundle\Tests\ResponsePopulator;

use Zeroem\RemoteHttpKernelBundle\ResponsePopulator\PopulateContent;
use Symfony\Component\HttpFoundation\Response;

class PopulatContentTest extends \PHPUnit_Framework_TestCase
{
    const THE_BODY="This is a body...";

    public function testResponseContentGetsPopulated() {
        $response = new Response();
        $populator = new PopluateContent($response);

        $popluator->populate(null,self::THE_BODY);

        $this->assertEqual(self::THE_BODY,$response->getContent());
    }
}