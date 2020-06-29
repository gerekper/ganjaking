<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Methods related to cart discount rules
 *
 * @class RP_WCDPD_Cart_Discounts
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
class RP_WCDPD_Cart_Discounts
{

    /**
     * Get coupon data
     *
     * @access public
     * @param array $data
     * @return array
     */
    public static function get_coupon_data_array($data)
    {
        // Amount is mandatory
        if (!isset($data['amount'])) {
            return false;
        }

        // Individual use property
        $data['individual_use'] = !RP_WCDPD_Settings::get('cart_discounts_allow_coupons');

        // Return coupon data array
        return apply_filters('rp_wcdpd_cart_discount_coupon_data', array_merge(array(
            'id'                            => PHP_INT_MAX,
            'type'                          => (RightPress_Help::wc_version_gte('3.4') ? 'rightpress_fixed_cart' : 'fixed_cart'),
            'discount_type'                 => (RightPress_Help::wc_version_gte('3.4') ? 'rightpress_fixed_cart' : 'fixed_cart'),
            'amount'                        => 0,
            'product_ids'                   => array(),
            'exclude_product_ids'           => array(),
            'usage_limit'                   => 0,
            'usage_limit_per_user'          => 0,
            'limit_usage_to_x_items'        => null,
            'usage_count'                   => 0,
            'expiry_date'                   => '',
            'apply_before_tax'              => 'yes',
            'free_shipping'                 => false,
            'product_categories'            => array(),
            'exclude_product_categories'    => array(),
            'exclude_sale_items'            => false,
            'minimum_amount'                => '',
            'maximum_amount'                => '',
            'customer_email'                => array(),
        ), $data));
    }

    /**
     * Get cart discount virtual coupon value html
     *
     * @access public
     * @param object $coupon
     * @return string
     */
    public static function get_cart_discount_coupon_html($coupon)
    {
        // Get coupon code
        $code = $coupon->get_code();

        // Get coupon discount amount
        $amount = WC()->cart->get_coupon_discount_amount($code, WC()->cart->display_cart_ex_tax);

        // Format html
        return apply_filters('woocommerce_coupon_discount_amount_html', ('-' . wc_price($amount)), $coupon);
    }



}
