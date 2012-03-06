<?php

namespace Zeroem\CurlBundle\Tests\Curl;

use Zeroem\CurlBundle\Curl\MultiManager;
use Zeroem\CurlBundle\Curl\Request;

class MultiManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testMutliManager() {
        $mm = new MultiManager();

        $urls = array(
            "http://symfony.com",
            "http://www.google.com",
        );

        $requests = array();

        foreach($urls as $url) {
            $request = new Request($url);
            $request->setOption(CURLOPT_RETURNTRANSFER,true);
            $requests[] = $request;
            $mm->addRequest($request);
        }

        $mm->execute();

        foreach($requests as $request) {
            $content = $mm->getContent($request);
            $this->assertTrue(!empty($content));
        }
    }
}