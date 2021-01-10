<?php

namespace WPMailSMTP\Vendor\Aws\ClientSideMonitoring\Exception;

use WPMailSMTP\Vendor\Aws\HasMonitoringEventsTrait;
use WPMailSMTP\Vendor\Aws\MonitoringEventsInterface;
/**
 * Represents an error interacting with configuration for client-side monitoring.
 */
class ConfigurationException extends \RuntimeException implements \WPMailSMTP\Vendor\Aws\MonitoringEventsInterface
{
    use HasMonitoringEventsTrait;
}
