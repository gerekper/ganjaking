<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Condition: Customer - Capability
 *
 * @class RP_WCDPD_Condition_Customer_Capability
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
class RP_WCDPD_Condition_Customer_Capability extends RightPress_Condition_Customer_Capability
{

    protected $plugin_prefix = RP_WCDPD_PLUGIN_PRIVATE_PREFIX;

    protected $contexts = array(
        'product_pricing',
        'cart_discounts',
        'checkout_fees',
    );

    // Singleton instance
    protected static $instance = false;

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        parent::__construct();
    }

    /**
     * Get value to compare against condition
     *
     * Legacy filter support
     *
     * @access public
     * @param array $params
     * @return mixed
     */
    public function get_value($params)
    {

        return apply_filters('rp_wcdpd_current_user_capabilities', parent::get_value($params));
    }





}

RP_WCDPD_Condition_Customer_Capability::get_instance();
