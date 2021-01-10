<?php

namespace WPMailSMTP\Vendor\Aws\EndpointDiscovery\Exception;

use WPMailSMTP\Vendor\Aws\HasMonitoringEventsTrait;
use WPMailSMTP\Vendor\Aws\MonitoringEventsInterface;
/**
 * Represents an error interacting with configuration for endpoint discovery
 */
class ConfigurationException extends \RuntimeException implements \WPMailSMTP\Vendor\Aws\MonitoringEventsInterface
{
    use HasMonitoringEventsTrait;
}
