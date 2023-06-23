<?php
/**
 * @license Apache-2.0
 *
 * Modified by woocommerce on 14-June-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

if (class_exists('Automattic_WooCommerce_Bookings_Vendor_Google_Client', false)) {
    // Prevent error with preloading in PHP 7.4
    // @see https://github.com/googleapis/google-api-php-client/issues/1976
    return;
}

$classMap = [
    'Automattic\WooCommerce\Bookings\Vendor\Google\\Client' => 'Automattic_WooCommerce_Bookings_Vendor_Google_Client',
    'Automattic\WooCommerce\Bookings\Vendor\Google\\Service' => 'Automattic_WooCommerce_Bookings_Vendor_Google_Service',
    'Automattic\WooCommerce\Bookings\Vendor\Google\\AccessToken\\Revoke' => 'Automattic_WooCommerce_Bookings_Vendor_Google_AccessToken_Revoke',
    'Automattic\WooCommerce\Bookings\Vendor\Google\\AccessToken\\Verify' => 'Automattic_WooCommerce_Bookings_Vendor_Google_AccessToken_Verify',
    'Automattic\WooCommerce\Bookings\Vendor\Google\\Model' => 'Automattic_WooCommerce_Bookings_Vendor_Google_Model',
    'Automattic\WooCommerce\Bookings\Vendor\Google\\Utils\\UriTemplate' => 'Automattic_WooCommerce_Bookings_Vendor_Google_Utils_UriTemplate',
    'Automattic\WooCommerce\Bookings\Vendor\Google\\AuthHandler\\Guzzle6AuthHandler' => 'Automattic_WooCommerce_Bookings_Vendor_Google_AuthHandler_Guzzle6AuthHandler',
    'Automattic\WooCommerce\Bookings\Vendor\Google\\AuthHandler\\Guzzle7AuthHandler' => 'Automattic_WooCommerce_Bookings_Vendor_Google_AuthHandler_Guzzle7AuthHandler',
    'Automattic\WooCommerce\Bookings\Vendor\Google\\AuthHandler\\Guzzle5AuthHandler' => 'Automattic_WooCommerce_Bookings_Vendor_Google_AuthHandler_Guzzle5AuthHandler',
    'Automattic\WooCommerce\Bookings\Vendor\Google\\AuthHandler\\AuthHandlerFactory' => 'Automattic_WooCommerce_Bookings_Vendor_Google_AuthHandler_AuthHandlerFactory',
    'Automattic\WooCommerce\Bookings\Vendor\Google\\Http\\Batch' => 'Automattic_WooCommerce_Bookings_Vendor_Google_Http_Batch',
    'Automattic\WooCommerce\Bookings\Vendor\Google\\Http\\MediaFileUpload' => 'Automattic_WooCommerce_Bookings_Vendor_Google_Http_MediaFileUpload',
    'Automattic\WooCommerce\Bookings\Vendor\Google\\Http\\REST' => 'Automattic_WooCommerce_Bookings_Vendor_Google_Http_REST',
    'Automattic\WooCommerce\Bookings\Vendor\Google\\Task\\Retryable' => 'Automattic_WooCommerce_Bookings_Vendor_Google_Task_Retryable',
    'Automattic\WooCommerce\Bookings\Vendor\Google\\Task\\Exception' => 'Automattic_WooCommerce_Bookings_Vendor_Google_Task_Exception',
    'Automattic\WooCommerce\Bookings\Vendor\Google\\Task\\Runner' => 'Automattic_WooCommerce_Bookings_Vendor_Google_Task_Runner',
    'Automattic\WooCommerce\Bookings\Vendor\Google\\Collection' => 'Automattic_WooCommerce_Bookings_Vendor_Google_Collection',
    'Automattic\WooCommerce\Bookings\Vendor\Google\\Service\\Exception' => 'Automattic_WooCommerce_Bookings_Vendor_Google_Service_Exception',
    'Automattic\WooCommerce\Bookings\Vendor\Google\\Service\\Resource' => 'Automattic_WooCommerce_Bookings_Vendor_Google_Service_Resource',
    'Automattic\WooCommerce\Bookings\Vendor\Google\\Exception' => 'Automattic_WooCommerce_Bookings_Vendor_Google_Exception',
];

foreach ($classMap as $class => $alias) {
    class_alias($class, $alias);
}

/**
 * This class needs to be defined explicitly as scripts must be recognized by
 * the autoloader.
 */
class Automattic_WooCommerce_Bookings_Vendor_Google_Task_Composer extends \Automattic\WooCommerce\Bookings\Vendor\Google\Task\Composer
{
}

/** @phpstan-ignore-next-line */
if (\false) {
    class Automattic_WooCommerce_Bookings_Vendor_Google_AccessToken_Revoke extends \Automattic\WooCommerce\Bookings\Vendor\Google\AccessToken\Revoke
    {
    }
    class Automattic_WooCommerce_Bookings_Vendor_Google_AccessToken_Verify extends \Automattic\WooCommerce\Bookings\Vendor\Google\AccessToken\Verify
    {
    }
    class Automattic_WooCommerce_Bookings_Vendor_Google_AuthHandler_AuthHandlerFactory extends \Automattic\WooCommerce\Bookings\Vendor\Google\AuthHandler\AuthHandlerFactory
    {
    }
    class Automattic_WooCommerce_Bookings_Vendor_Google_AuthHandler_Guzzle5AuthHandler extends \Automattic\WooCommerce\Bookings\Vendor\Google\AuthHandler\Guzzle5AuthHandler
    {
    }
    class Automattic_WooCommerce_Bookings_Vendor_Google_AuthHandler_Guzzle6AuthHandler extends \Automattic\WooCommerce\Bookings\Vendor\Google\AuthHandler\Guzzle6AuthHandler
    {
    }
    class Automattic_WooCommerce_Bookings_Vendor_Google_AuthHandler_Guzzle7AuthHandler extends \Automattic\WooCommerce\Bookings\Vendor\Google\AuthHandler\Guzzle7AuthHandler
    {
    }
    class Automattic_WooCommerce_Bookings_Vendor_Google_Client extends \Automattic\WooCommerce\Bookings\Vendor\Google\Client
    {
    }
    class Automattic_WooCommerce_Bookings_Vendor_Google_Collection extends \Automattic\WooCommerce\Bookings\Vendor\Google\Collection
    {
    }
    class Automattic_WooCommerce_Bookings_Vendor_Google_Exception extends \Automattic\WooCommerce\Bookings\Vendor\Google\Exception
    {
    }
    class Automattic_WooCommerce_Bookings_Vendor_Google_Http_Batch extends \Automattic\WooCommerce\Bookings\Vendor\Google\Http\Batch
    {
    }
    class Automattic_WooCommerce_Bookings_Vendor_Google_Http_MediaFileUpload extends \Automattic\WooCommerce\Bookings\Vendor\Google\Http\MediaFileUpload
    {
    }
    class Automattic_WooCommerce_Bookings_Vendor_Google_Http_REST extends \Automattic\WooCommerce\Bookings\Vendor\Google\Http\REST
    {
    }
    class Automattic_WooCommerce_Bookings_Vendor_Google_Model extends \Automattic\WooCommerce\Bookings\Vendor\Google\Model
    {
    }
    class Automattic_WooCommerce_Bookings_Vendor_Google_Service extends \Automattic\WooCommerce\Bookings\Vendor\Google\Service
    {
    }
    class Automattic_WooCommerce_Bookings_Vendor_Google_Service_Exception extends \Automattic\WooCommerce\Bookings\Vendor\Google\Service\Exception
    {
    }
    class Automattic_WooCommerce_Bookings_Vendor_Google_Service_Resource extends \Automattic\WooCommerce\Bookings\Vendor\Google\Service\Resource
    {
    }
    class Automattic_WooCommerce_Bookings_Vendor_Google_Task_Exception extends \Automattic\WooCommerce\Bookings\Vendor\Google\Task\Exception
    {
    }
    interface Automattic_WooCommerce_Bookings_Vendor_Google_Task_Retryable extends \Automattic\WooCommerce\Bookings\Vendor\Google\Task\Retryable
    {
    }
    class Automattic_WooCommerce_Bookings_Vendor_Google_Task_Runner extends \Automattic\WooCommerce\Bookings\Vendor\Google\Task\Runner
    {
    }
    class Automattic_WooCommerce_Bookings_Vendor_Google_Utils_UriTemplate extends \Automattic\WooCommerce\Bookings\Vendor\Google\Utils\UriTemplate
    {
    }
}
