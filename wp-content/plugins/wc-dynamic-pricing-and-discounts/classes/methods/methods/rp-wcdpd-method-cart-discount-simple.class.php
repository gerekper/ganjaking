<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load dependencies
if (!class_exists('RP_WCDPD_Method_Cart_Discount')) {
    require_once('rp-wcdpd-method-cart-discount.class.php');
}

/**
 * Cart Discount Method: Simple
 *
 * @class RP_WCDPD_Method_Cart_Discount_Simple
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
class RP_WCDPD_Method_Cart_Discount_Simple extends RP_WCDPD_Method_Cart_Discount
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
        return esc_html__('Simple', 'rp_wcdpd');
    }

    /**
     * Get label
     *
     * @access public
     * @return string
     */
    public function get_label()
    {
        return esc_html__('Simple discount', 'rp_wcdpd');
    }

    /**
     * Apply cart discount
     *
     * Note: this implementation uses virtual (non-existing in database) coupons,
     * coupon data is later provided via woocommerce_get_shop_coupon_data hook
     *
     * @access public
     * @param array $adjustment
     * @return void
     */
    public function apply_adjustment($adjustment)
    {

        // Load controller
        $controller = RP_WCDPD_Controller_Methods_Cart_Discount::get_instance();

        // Use Rule UID as virtual coupon code
        $coupon_code = $adjustment['rule']['uid'];

        // Load virtual coupon
        try {
            $virtual_coupon = new WC_Coupon($coupon_code);
        }
        // Something went wrong
        catch (Exception $e) {
            return;
        }

        // Check if valid coupon was loaded and is not yet used in cart
        if ($virtual_coupon->is_valid() && !WC()->cart->has_discount($coupon_code)) {


            // Apply coupon
            WC()->cart->applied_coupons[] = $coupon_code;

            // Recalculate totals
            $controller->applying_cart_discount = true;
            do_action('woocommerce_applied_coupon', $coupon_code);
            $controller->applying_cart_discount = false;

            // Remove regular coupons if they are not allowed
            if (!RP_WCDPD_Settings::get('cart_discounts_allow_coupons')) {
                RP_WCDPD_WC_Cart::remove_all_regular_coupons();
            }
        }
    }

    /**
     * Get virtual coupon data
     *
     * @access public
     * @param array $adjustment
     * @return array
     */
    public function get_coupon_data($adjustment)
    {
        // Type mapping
        $types = array(
            'discount__amount'                      => (RightPress_Help::wc_version_gte('3.4') ? 'rightpress_fixed_cart' : 'fixed_cart'),
            'discount_per_cart_item__amount'        => (RightPress_Help::wc_version_gte('3.4') ? 'rightpress_fixed_cart' : 'fixed_cart'),
            'discount_per_cart_line__amount'        => (RightPress_Help::wc_version_gte('3.4') ? 'rightpress_fixed_cart' : 'fixed_cart'),
            'discount__percentage'                  => 'percent',
            'discount_per_cart_item__percentage'    => 'percent',
        );

        // Get coupon type
        $type = $types[$adjustment['rule']['pricing_method']];

        // Get coupon amount
        $amount = in_array($type, array('percent', 'percent_product'), true) ? $adjustment['rule']['pricing_value'] : $adjustment['adjustment_amount'];

        // Get coupon data
        return RP_WCDPD_Cart_Discounts::get_coupon_data_array(array(
            'type'          => $type,
            'discount_type' => $type,
            'amount'        => $amount,
        ));
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

        // Get discount amount
        $discount_amount = RP_WCDPD_Pricing::get_adjustment_value($adjustment['rule']['pricing_method'], $adjustment['rule']['pricing_value'], $cart_subtotal, $adjustment);

        // Allow developers to override
        $discount_amount = apply_filters('rp_wcdpd_cart_discount_amount', $discount_amount, $adjustment);

        // Return discount amount
        return (float) abs($discount_amount);
    }

    /**
     * Get virtual coupon label
     *
     * @access public
     * @param array $adjustment
     * @return string
     */
    public function get_coupon_label($adjustment)
    {
        return !RightPress_Help::is_empty($adjustment['rule']['title']) ? $adjustment['rule']['title'] : esc_html__('Discount', 'rp_wcdpd');
    }


}

RP_WCDPD_Method_Cart_Discount_Simple::get_instance();
