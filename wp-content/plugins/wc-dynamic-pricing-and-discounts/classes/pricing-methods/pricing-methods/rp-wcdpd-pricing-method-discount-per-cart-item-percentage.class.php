<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load dependencies
if (!class_exists('RP_WCDPD_Pricing_Method_Discount_Per_Cart_Item')) {
    require_once('rp-wcdpd-pricing-method-discount-per-cart-item.class.php');
}

/**
 * Pricing Method: Discount Per Cart Item - Percentage
 *
 * @class RP_WCDPD_Pricing_Method_Discount_Per_Cart_Item_Percentage
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
class RP_WCDPD_Pricing_Method_Discount_Per_Cart_Item_Percentage extends RP_WCDPD_Pricing_Method_Discount_Per_Cart_Item
{

    protected $key      = 'percentage';
    protected $contexts = array('cart_discounts_simple');
    protected $position = 20;

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
        return esc_html__('Percentage discount per cart item', 'rp_wcdpd');
    }

    /**
     * Calculate adjustment value
     *
     * @access public
     * @param float $setting
     * @param float $amount
     * @param array $adjustment
     * @return float
     */
    public function calculate($setting, $amount = 0, $adjustment = null)
    {
        // Get conditions
        $conditions = (is_array($adjustment) && !empty($adjustment['rule']['conditions'])) ? $adjustment['rule']['conditions'] : array();

        // Check if taxes need to be included
        $include_tax = wc_prices_include_tax();

        // Get cart item subtotal for calculation
        $subtotal = RP_WCDPD_Controller_Conditions::get_sum_of_cart_item_subtotals_by_product_conditions($conditions, $include_tax);

        // Calculate adjustment
        return -1 * (float) ($subtotal * $setting / 100);
    }





}

RP_WCDPD_Pricing_Method_Discount_Per_Cart_Item_Percentage::get_instance();
