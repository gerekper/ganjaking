<?php

namespace WPMailSMTP\Vendor\Aws;

use WPMailSMTP\Vendor\Psr\Http\Message\ResponseInterface;
interface ResponseContainerInterface
{
    /**
     * Get the received HTTP response if any.
     *
     * @return ResponseInterface|null
     */
    public function getResponse();
}
