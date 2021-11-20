<?php

namespace WPMailSMTP\Vendor\Aws\Endpoint\UseDualstackEndpoint\Exception;

use WPMailSMTP\Vendor\Aws\HasMonitoringEventsTrait;
use WPMailSMTP\Vendor\Aws\MonitoringEventsInterface;
/**
 * Represents an error interacting with configuration for useDualstackRegion
 */
class ConfigurationException extends \RuntimeException implements \WPMailSMTP\Vendor\Aws\MonitoringEventsInterface
{
    use HasMonitoringEventsTrait;
}
