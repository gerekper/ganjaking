<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load dependencies
if (!class_exists('RP_WCDPD_Pricing_Method_Fixed')) {
    require_once('rp-wcdpd-pricing-method-fixed.class.php');
}

/**
 * Pricing Method: Fixed - Price Per Range
 *
 * @class RP_WCDPD_Pricing_Method_Fixed_Price_Per_Range
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
class RP_WCDPD_Pricing_Method_Fixed_Price_Per_Range extends RP_WCDPD_Pricing_Method_Fixed
{

    protected $key      = 'price_per_range';
    protected $contexts = array('product_pricing_volume');
    protected $position = 40;

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
        return esc_html__('Fixed price per range', 'rp_wcdpd');
    }





}

RP_WCDPD_Pricing_Method_Fixed_Price_Per_Range::get_instance();
