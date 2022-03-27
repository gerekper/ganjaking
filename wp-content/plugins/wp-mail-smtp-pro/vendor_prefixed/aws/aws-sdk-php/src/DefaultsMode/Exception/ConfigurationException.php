<?php

namespace WPMailSMTP\Vendor\Aws\DefaultsMode\Exception;

use WPMailSMTP\Vendor\Aws\HasMonitoringEventsTrait;
use WPMailSMTP\Vendor\Aws\MonitoringEventsInterface;
/**
 * Represents an error interacting with configuration mode
 */
class ConfigurationException extends \RuntimeException implements \WPMailSMTP\Vendor\Aws\MonitoringEventsInterface
{
    use HasMonitoringEventsTrait;
}
