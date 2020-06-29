<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Condition: Cart Item Quantities - Product Shipping Classes
 *
 * @class RP_WCDPD_Condition_Cart_Item_Quantities_Product_Shipping_Classes
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
class RP_WCDPD_Condition_Cart_Item_Quantities_Product_Shipping_Classes extends RightPress_Condition_Cart_Item_Quantities_Product_Shipping_Classes
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





}

RP_WCDPD_Condition_Cart_Item_Quantities_Product_Shipping_Classes::get_instance();
