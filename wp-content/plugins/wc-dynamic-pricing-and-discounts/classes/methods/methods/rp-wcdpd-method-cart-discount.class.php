<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load dependencies
if (!class_exists('RP_WCDPD_Method')) {
    require_once('rp-wcdpd-method.class.php');
}

/**
 * Cart Discount Method
 *
 * @class RP_WCDPD_Method_Cart_Discount
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
abstract class RP_WCDPD_Method_Cart_Discount extends RP_WCDPD_Method
{

    protected $context = 'cart_discounts';

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
     * Get cart subtotal
     *
     * @access public
     * @param bool $for_limit
     * @return float
     */
    public function get_cart_subtotal($for_limit = false)
    {
        $include_tax = wc_prices_include_tax();
        return RightPress_Help::calculate_subtotal($include_tax);
    }


}
