<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Condition: Product Other - WooCommerce Coupons Applied
 *
 * @class RP_WCDPD_Condition_Product_Other_WC_Coupons_Applied
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
class RP_WCDPD_Condition_Product_Other_WC_Coupons_Applied extends RightPress_Condition_Product_Other_WC_Coupons_Applied
{

    protected $plugin_prefix = RP_WCDPD_PLUGIN_PRIVATE_PREFIX;

    protected $contexts = array(
        'product_pricing_product',
        'product_pricing_bogo_product',
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

RP_WCDPD_Condition_Product_Other_WC_Coupons_Applied::get_instance();
