<?php

/*
 * (c) Darrell Hamilton <darrell.noice@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zeroem\CurlBundle\Curl;

use Symfony\Component\EventDispatcher\Event;

class MultiInfoEvent extends Event
{
    private $manager;
    private $request;
    private $info;


    public function __construct(MultiManager $manager, Request $request, MultiInfo $info) {
        $this->manager = $manager;
        $this->request = $request;
        $this->info = $info;
    }

    public function getManager() {
        return $this->manager;
    }

    public function getRequest() {
        return $this->request;
    }

    public function getInfo() {
        return $this->info;
    }
}