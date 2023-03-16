<?php

// For older (pre-2.7.2) verions of google/apiclient
if (
    file_exists(__DIR__ . '/../apiclient/src/Google/Client.php')
    && !class_exists('Automattic_WooCommerce_Bookings_Vendor_Google_Client', false)
) {
    require_once(__DIR__ . '/../apiclient/src/Google/Client.php');
    if (
        defined('Automattic_WooCommerce_Bookings_Vendor_Google_Client::LIBVER')
        && version_compare(Automattic_WooCommerce_Bookings_Vendor_Google_Client::LIBVER, '2.7.2', '<=')
    ) {
        $servicesClassMap = [
            'Automattic\WooCommerce\Bookings\Vendor\Google\\Client' => 'Automattic_WooCommerce_Bookings_Vendor_Google_Client',
            'Automattic\WooCommerce\Bookings\Vendor\Google\\Service' => 'Automattic_WooCommerce_Bookings_Vendor_Google_Service',
            'Automattic\WooCommerce\Bookings\Vendor\Google\\Service\\Resource' => 'Automattic_WooCommerce_Bookings_Vendor_Google_Service_Resource',
            'Automattic\WooCommerce\Bookings\Vendor\Google\\Model' => 'Automattic_WooCommerce_Bookings_Vendor_Google_Model',
            'Automattic\WooCommerce\Bookings\Vendor\Google\\Collection' => 'Automattic_WooCommerce_Bookings_Vendor_Google_Collection',
        ];
        foreach ($servicesClassMap as $alias => $class) {
            class_alias($class, $alias);
        }
    }
}
spl_autoload_register(function ($class) {
    if (0 === strpos($class, 'Google_Service_')) {
        // Autoload the new class, which will also create an alias for the
        // old class by changing underscores to namespaces:
        //     Google_Service_Speech_Resource_Operations
        //      => Automattic\WooCommerce\Bookings\Vendor\Google\Service\Speech\Resource\Operations
        $classExists = class_exists($newClass = str_replace('_', '\\', $class));
        if ($classExists) {
            return true;
        }
    }
}, true, true);
