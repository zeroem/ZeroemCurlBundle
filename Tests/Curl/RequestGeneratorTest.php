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

class RequestGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testSetAndUnsetOptions() {
        $generator = new RequestGenerator();

        $generator->addOption(CURLOPT_RETURNTRANSFER,true);

        // first removeOption should return the value
        $this->assertTrue($generator->removeOption(CURLOPT_RETURNTRANSFER));

        // now that it's already been removed, it should return false
        $this->assertFalse($generator->removeOption(CURLOPT_RETURNTRANSFER));
    }
}