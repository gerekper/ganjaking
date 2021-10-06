<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Methods related to price and discount calculations
 *
 * @class RP_WCDPD_Pricing
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
class RP_WCDPD_Pricing
{

    /**
     * =================================================================================================================
     * VOLUME PRICING RULES QUANTITY RANGES
     * =================================================================================================================
     */

    /**
     * Add missing quantity ranges (gaps in continuity) for volume pricing rules
     *
     * @access public
     * @param array $quantity_ranges
     * @param array $add_closing_range
     * @return array
     */
    public static function add_volume_pricing_rule_missing_quantity_ranges($quantity_ranges, $add_closing_range = true)
    {
        $fixed = array();

        $last_from = null;
        $last_to = null;

        foreach ($quantity_ranges as $quantity_range) {

            // Get from and to
            $from = $quantity_range['from'];
            $to = $quantity_range['to'];

            // Maybe add first range
            if ($last_from === null && $from > 1) {
                $fixed[] = RP_WCDPD_Pricing::get_volume_pricing_rule_missing_quantity_range(1, ($from - 1));
            }

            // Gap between last to and current from
            if ($last_to !== null && ($from - $last_to) > 1) {
                $fixed[] = RP_WCDPD_Pricing::get_volume_pricing_rule_missing_quantity_range(($last_to + 1), ($from - 1));
            }

            // Add current range
            $fixed[] = $quantity_range;

            // Set last from and to
            $last_from = $from;
            $last_to = $to;
        }

        // Add closing range
        if ($last_to !== null && $add_closing_range) {
            $fixed[] = RP_WCDPD_Pricing::get_volume_pricing_rule_missing_quantity_range(($last_to + 1), null);
        }

        return $fixed;
    }

    /**
     * Get missing quantity range for volume pricing rule
     *
     * @access public
     * @param int $from
     * @param int $to
     * @return array
     */
    public static function get_volume_pricing_rule_missing_quantity_range($from, $to)
    {
        return array(
            'uid'               => null,
            'from'              => $from,
            'to'                => $to,
            'pricing_method'    => 'discount__amount',
            'pricing_value'     => 0,
            'is_missing_range'  => true,
        );
    }

    /**
     * =================================================================================================================
     * OTHER METHODS
     * =================================================================================================================
     */

    /**
     * Get pricing methods for display
     *
     * @access public
     * @param string $context
     * @return array
     */
    public static function get_pricing_methods_for_display($context = null)
    {
        return RP_WCDPD_Controller_Pricing_Methods::get_items_for_display($context);
    }

    /**
     * Check if pricing method exists
     *
     * @access public
     * @param string $combined_key
     * @param string $context
     * @return bool
     */
    public static function pricing_method_exists($combined_key, $context = null)
    {
        return RP_WCDPD_Controller_Pricing_Methods::item_exists($combined_key, $context);
    }

    /**
     * Get adjustment value
     *
     * @access public
     * @param string $combined_key
     * @param float $setting
     * @param float $amount
     * @param array $adjustment
     * @return float
     */
    public static function get_adjustment_value($combined_key, $setting, $amount = 0, $adjustment = null)
    {
        // Load pricing method
        if ($pricing_method = RP_WCDPD_Controller_Pricing_Methods::get_item($combined_key)) {
            return $pricing_method->calculate($setting, $amount, $adjustment);
        }

        return 0;
    }

    /**
     * Adjusted amount
     *
     * @access public
     * @param float $amount
     * @param string $combined_key
     * @param float $setting
     * @return float
     */
    public static function adjust_amount($amount, $combined_key, $setting)
    {
        // Load pricing method
        if ($pricing_method = RP_WCDPD_Controller_Pricing_Methods::get_item($combined_key)) {
            return $pricing_method->adjust($amount, $setting);
        }

        return $amount;
    }

    /**
     * Get product base price
     *
     * @access public
     * @param object $product
     * @param string $cart_item_key
     * @param array $cart_item
     * @return float
     */
    public static function get_product_base_price($product, $cart_item_key, $cart_item)
    {

        // Product is on sale and regular price has to be used as base price
        if (RP_WCDPD_Settings::get('product_pricing_sale_price_handling') === 'regular' && RP_WCDPD_Product_Pricing::product_is_on_sale($product)) {
            $base_price = (float) $product->get_regular_price('edit');
        }
        // Otherwise always use final product price
        else {
            $base_price = (float) $product->get_price('edit');
        }

        // Allow developers to override
        $base_price = (float) apply_filters('rp_wcdpd_product_base_price', $base_price, $product, $cart_item_key, $cart_item);

        // Return base price
        return $base_price;
    }

    /**
     * Get pricing settings label by context
     *
     * @access public
     * @param string $context
     * @return string
     */
    public static function get_pricing_settings_label($context)
    {
        if ($context === 'product_pricing') {
            return esc_html__('Adjustment', 'rp_wcdpd');
        }
        else if ($context === 'cart_discounts') {
            return esc_html__('Discount', 'rp_wcdpd');
        }
        else if ($context === 'checkout_fees') {
            return esc_html__('Fee', 'rp_wcdpd');
        }
    }





}
