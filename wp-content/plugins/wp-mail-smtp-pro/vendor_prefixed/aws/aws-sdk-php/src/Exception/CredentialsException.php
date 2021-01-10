<?php

namespace WPMailSMTP\Vendor\Aws\Exception;

use WPMailSMTP\Vendor\Aws\HasMonitoringEventsTrait;
use WPMailSMTP\Vendor\Aws\MonitoringEventsInterface;
class CredentialsException extends \RuntimeException implements \WPMailSMTP\Vendor\Aws\MonitoringEventsInterface
{
    use HasMonitoringEventsTrait;
}
