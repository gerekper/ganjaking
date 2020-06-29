<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Condition: Product - Variation
 *
 * @class RP_WCDPD_Condition_Product_Variation
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
class RP_WCDPD_Condition_Product_Variation extends RightPress_Condition_Product_Variation
{

    protected $plugin_prefix = RP_WCDPD_PLUGIN_PRIVATE_PREFIX;

    protected $contexts = array(
        'product_pricing_product',
        'product_pricing_bogo_product',
        'product_pricing_group_product',
        'cart_discounts_product',
        'checkout_fees_product',
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





}

RP_WCDPD_Condition_Product_Variation::get_instance();
