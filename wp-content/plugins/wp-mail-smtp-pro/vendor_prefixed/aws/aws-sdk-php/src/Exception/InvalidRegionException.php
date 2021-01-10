<?php

namespace WPMailSMTP\Vendor\Aws\Exception;

use WPMailSMTP\Vendor\Aws\HasMonitoringEventsTrait;
use WPMailSMTP\Vendor\Aws\MonitoringEventsInterface;
class InvalidRegionException extends \RuntimeException implements \WPMailSMTP\Vendor\Aws\MonitoringEventsInterface
{
    use HasMonitoringEventsTrait;
}
