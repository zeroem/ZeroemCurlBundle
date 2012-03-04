<?php

/*
 * (c) Darrell Hamilton <darrell.noice@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zeroem\CurlBundle\Tests\Curl;

use Zeroem\CurlBundle\Curl\RequestGenerator;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RequestGeneratorTest extends WebTestCase
{
    
    public function testSetAndUnsetOptions() {
        $generator = new RequestGenerator();

        $generator->addOption(CURLOPT_RETURNTRANSFER,true);

        // first removeOption should return the value
        $this->assertTrue($generator->removeOption(CURLOPT_RETURNTRANSFER));

        // now that it's already been removed, it should return false
        $this->assertFalse($generator->removeOption(CURLOPT_RETURNTRANSFER));
    }

    public function testServiceConfiguration() {
        $client = static::createClient();
        $container = $client->getContainer();
        $generator = $container->get("request_generator");

        $request = $generator->getRequest();

        // not sure what to do with this as curl doesn't provide any means of
        // checking what options have been set
        // $this->assert...
    }
}