<?php

/*
 * (c) Darrell Hamilton <darrell.noice@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zeroem\CurlBundle\Tests\Curl;

use Zeroem\CurlBundle\Curl\MultiManager;
use Zeroem\CurlBundle\Curl\Request;
use Zeroem\CurlBundle\Curl\CurlEvents;
use Zeroem\CurlBundle\Curl\MultiInfoEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

class MultiManagerTest extends \PHPUnit_Framework_TestCase
{
    private $urls = array(
        "http://symfony.com",
        "http://www.google.com",
    );

    private $count=0;

    public function testCanFindRequestFromHandle() {
        $mm = new MultiManager();

        $requests = $this->makeRequestList($this->urls);

        foreach($requests as $request) {
            $mm->addRequest($request);
            $this->assertEquals($request, $mm->findRequest($request->getHandle()));
        }
    }

    public function testMutliManager() {
        $dispatcher = new EventDispatcher();
        $dispatcher->addListener(
            CurlEvents::MULTI_INFO,
            array($this,"handleMultiInfoEvent")
        );

        $mm = new MultiManager($dispatcher);
        $requests = $this->makeRequestList($this->urls);

        foreach($requests as $request) {
            $mm->addRequest($request);
        }

        $mm->execute();

        // verify there were events for each url
        $this->assertEquals(count($this->urls),$this->count);

        foreach($requests as $request) {
            $content = $mm->getContent($request);
            $this->assertTrue(!empty($content));
            $this->assertEquals(200, $request->getInfo(CURLINFO_HTTP_CODE));
        }
    }

    private function makeRequestList(array $urls) {
        $request = array();
        foreach($urls as $url) {
            $request = new Request($url);
            $request->setOption(CURLOPT_RETURNTRANSFER,true);
            $requests[] = $request;
        }

        return $requests;
    }

    public function handleMultiInfoEvent(MultiInfoEvent $e) {
         $this->count++;
    }
}