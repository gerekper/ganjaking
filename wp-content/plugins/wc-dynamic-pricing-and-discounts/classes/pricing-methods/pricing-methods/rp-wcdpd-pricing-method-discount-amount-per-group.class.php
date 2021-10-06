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
 * Pricing Method: Discount - Amount Per Group
 *
 * @class RP_WCDPD_Pricing_Method_Discount_Amount_Per_Group
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
class RP_WCDPD_Pricing_Method_Discount_Amount_Per_Group extends RP_WCDPD_Pricing_Method_Discount
{

    protected $key      = 'amount_per_group';
    protected $contexts = array('product_pricing_group');
    protected $position = 30;

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
        return esc_html__('Fixed discount per group', 'rp_wcdpd');
    }





}

RP_WCDPD_Pricing_Method_Discount_Amount_Per_Group::get_instance();
