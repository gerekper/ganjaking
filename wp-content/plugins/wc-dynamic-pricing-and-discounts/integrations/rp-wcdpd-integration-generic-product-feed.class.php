<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Generic integration with Product Feed plugins
 *
 * Product Feed plugin developers must add support for this by hooking into filter rp_wcdpd_request_is_product_feed
 *
 * @class RP_WCDPD_Integration_Generic_Product_Feed
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
class RP_WCDPD_Integration_Generic_Product_Feed
{

    // Singleton control
    protected static $instance = false; public static function get_instance() { return self::$instance ? self::$instance : (self::$instance = new self()); }

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        // Set up price hooks
        RightPress_Help::add_early_filter('woocommerce_product_get_price', array($this, 'get_price'), 2);
        RightPress_Help::add_early_filter('woocommerce_product_variation_get_price', array($this, 'get_price'),  2);
    }

    /**
     * Get price
     *
     * @access public
     * @param float $price
     * @param object $product
     * @return float|string
     */
    public function get_price($price, $product)
    {

        // Check if any 3rd party plugin defines this request as a product feed request
        if (apply_filters('rp_wcdpd_request_is_product_feed', false, $price, $product)) {

            // Apply simple product pricing rules to product price
            $price = RP_WCDPD_Product_Pricing::apply_simple_product_pricing_rules_to_product_price($price, $product);
        }

        return $price;
    }





}

RP_WCDPD_Integration_Generic_Product_Feed::get_instance();
