<?php

/*
 * (c) Darrell Hamilton <darrell.noice@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Curl\ResponsePopulator;

use Symfony\Component\HttpFoundation\Response;

class PopulateContent extends AbstractPopulator
{
    public function populate() {
        list($handle, $content) = func_get_args();

        $this->response->setContent($this->response->getContent().$content);

        return strlen($content);
    }
}

