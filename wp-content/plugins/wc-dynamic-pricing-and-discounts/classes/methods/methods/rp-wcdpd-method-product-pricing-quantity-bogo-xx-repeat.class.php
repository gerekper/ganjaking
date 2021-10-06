<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load dependencies
if (!class_exists('RP_WCDPD_Method_Product_Pricing_Quantity_BOGO_XX')) {
    require_once('rp-wcdpd-method-product-pricing-quantity-bogo-xx.class.php');
}

/**
 * Product Pricing Method: BOGO XX Repeat
 *
 * @class RP_WCDPD_Method_Product_Pricing_Quantity_BOGO_XX_Repeat
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
class RP_WCDPD_Method_Product_Pricing_Quantity_BOGO_XX_Repeat extends RP_WCDPD_Method_Product_Pricing_Quantity_BOGO_XX
{

    protected $key      = 'bogo_xx_repeat';
    protected $position = 20;

    // Other properties
    protected $repeat = true;

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
        return esc_html__('Buy x get x - Repeating', 'rp_wcdpd');
    }


}

RP_WCDPD_Method_Product_Pricing_Quantity_BOGO_XX_Repeat::get_instance();
