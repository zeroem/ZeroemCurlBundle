<?php

namespace Zeroem\CurlBundle\Tests\Curl;

use Zeroem\CurlBundle\Curl\CurlOptions;

class CurlOptionsTest extends \PHPUnit_Framework_TestCase
{
    public function testIsValidOption() {
        $this->assertTrue(CurlOptions::isValidOption(CURLOPT_RETURNTRANSFER));
        $this->assertFalse(CurlOptions::isValidOption("herp derpity"));
    }

    /**
     * @dataProvider badOptions
     * @expectedException InvalidArgumentException
     */
    public function testInvalidOptions($option, $value) {
        CurlOptions::checkOptionValue($option, $value);
    }

    /**
     * @dataProvider goodOptions
     */
    public function testValidOptions($option, $value) {
        $this->assertTrue(CurlOptions::checkOptionValue($option,$value));
    }

    public function goodOptions() {
        return array(
            array(CURLOPT_AUTOREFERER,true),
            array(CURLOPT_BUFFERSIZE,10),
            array(CURLOPT_CAINFO,"a string"),
            array(CURLOPT_HTTP200ALIASES,array(200,404,401)),
        );
    }

    public function badOptions() {
        return array(
            array(CURLOPT_AUTOREFERER,"derp"),
            array(CURLOPT_BUFFERSIZE,"derp"),
            array(CURLOPT_CAINFO,false),
            array(CURLOPT_HTTP200ALIASES,""),
        );
    }
}
