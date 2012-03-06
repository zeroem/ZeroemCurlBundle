<?php

namespace Zeroem\CurlBundle\Tests\Curl;

use Zeroem\CurlBundle\Curl\MultiManager;
use Zeroem\CurlBundle\Curl\Request;

class MultiManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testMutliManager() {
        $mm = new MultiManager();

        $requests = array(
            new Request("http://symfony.com"),
            new Request("http://www.google.com"),
        );

        foreach($requests as $request) {
            $request->setOption(CURLOPT_RETURNTRANSFER,true);
            $mm->addRequest($request);
        }

        $mm->execute();

        foreach($requests as $request) {
            $content = $mm->getContent($request);
            $this->assertTrue(!empty($content));
        }
    }
}