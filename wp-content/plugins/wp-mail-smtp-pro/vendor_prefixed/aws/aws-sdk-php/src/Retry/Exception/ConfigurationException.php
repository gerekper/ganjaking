<?php

namespace WPMailSMTP\Vendor\Aws\Retry\Exception;

use WPMailSMTP\Vendor\Aws\HasMonitoringEventsTrait;
use WPMailSMTP\Vendor\Aws\MonitoringEventsInterface;
/**
 * Represents an error interacting with retry configuration
 */
class ConfigurationException extends \RuntimeException implements \WPMailSMTP\Vendor\Aws\MonitoringEventsInterface
{
    use HasMonitoringEventsTrait;
}
