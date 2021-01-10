<?php

namespace WPMailSMTP\Vendor\Aws\ClientSideMonitoring;

use WPMailSMTP\Vendor\Aws\CommandInterface;
use WPMailSMTP\Vendor\Aws\Exception\AwsException;
use WPMailSMTP\Vendor\Aws\ResultInterface;
use WPMailSMTP\Vendor\GuzzleHttp\Psr7\Request;
use WPMailSMTP\Vendor\Psr\Http\Message\RequestInterface;
/**
 * @internal
 */
interface MonitoringMiddlewareInterface
{
    /**
     * Data for event properties to be sent to the monitoring agent.
     *
     * @param RequestInterface $request
     * @return array
     */
    public static function getRequestData(\WPMailSMTP\Vendor\Psr\Http\Message\RequestInterface $request);
    /**
     * Data for event properties to be sent to the monitoring agent.
     *
     * @param ResultInterface|AwsException|\Exception $klass
     * @return array
     */
    public static function getResponseData($klass);
    public function __invoke(\WPMailSMTP\Vendor\Aws\CommandInterface $cmd, \WPMailSMTP\Vendor\Psr\Http\Message\RequestInterface $request);
}
