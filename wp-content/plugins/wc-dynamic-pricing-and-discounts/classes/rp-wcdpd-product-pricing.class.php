<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Methods related to product pricing rules
 *
 * @class RP_WCDPD_Product_Pricing
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
class RP_WCDPD_Product_Pricing
{

    // Singleton control
    protected static $instance = false; public static function get_instance() { return self::$instance ? self::$instance : (self::$instance = new self()); }

    // RightPress Product Price component hook position
    private $rightpress_hook_position = 50;

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        // Add prices array pointer
        add_filter('rightpress_product_price_breakdown_prices_array_pointers', array($this, 'add_prices_array_pointer'), $this->rightpress_hook_position);

        // Maybe display cart item full price
        add_filter('rightpress_product_price_display_full_price', array($this, 'maybe_display_cart_item_full_price'), $this->rightpress_hook_position);

        // Maybe add public rule description to cart item display price
        add_filter('rightpress_product_price_cart_item_product_display_price', array($this, 'maybe_add_public_rule_description_to_cart_item_display_price'), $this->rightpress_hook_position, 3);
    }

    /**
     * Add prices array pointer
     *
     * @access public
     * @param array $pointers
     * @return array
     */
    public function add_prices_array_pointer($pointers)
    {

        $pointers['rp_wcdpd'] = 1;
        return $pointers;
    }

    /**
     * Maybe display cart item full price
     *
     * @access public
     * @param bool $display
     * @return bool
     */
    public function maybe_display_cart_item_full_price($display)
    {

        if (RP_WCDPD_Settings::get('product_pricing_display_regular_price')) {
            $display = true;
        }

        return $display;
    }

    /**
     * Maybe add public rule description
     *
     * @access public
     * @param string $display_price
     * @param array $price_data
     * @param float $full_price
     * @return string
     */
    public function maybe_add_public_rule_description_to_cart_item_display_price($display_price, $price_data, $full_price)
    {

        // Check if any pricing rules were applied to this price
        if (!empty($price_data['all_changes']['rp_wcdpd'])) {

            // Check if prices differ
            if (RightPress_Product_Price_Display::display_prices_differ($price_data['price'], $full_price)) {

                // Get controller instance
                $controller = RP_WCDPD_Controller_Methods_Product_Pricing::get_instance();

                // Get list of rule uids
                $rule_uids = RP_WCDPD_Rules::get_rule_uids_from_adjustments($price_data['all_changes']['rp_wcdpd']);

                // Maybe add public description
                $display_price = $controller->maybe_add_public_description($display_price, $rule_uids);
            }
        }

        return $display_price;
    }

    /**
     * Remove cart items that are not affected by specific pricing rules
     *
     * @access public
     * @param array $cart_items
     * @param array $rules
     * @return array
     */
    public static function filter_items_by_rules($cart_items, $rules)
    {

        $filtered = array();
        $keys_added = array();

        // Iterate over cart items
        foreach ($cart_items as $cart_item_key => $cart_item) {

            // Cart item already in list
            if (in_array($cart_item_key, $keys_added, true)) {
                continue;
            }

            // Filter rules by conditions to leave those that apply to current cart item
            $current_rules = RP_WCDPD_Controller_Conditions::filter_objects($rules, array(
                'cart_item'     => $cart_item,
                'cart_items'    => $cart_items,
            ));

            // Add to results array if at least one rule applies to this cart item
            if (!empty($current_rules)) {
                $filtered[$cart_item_key] = $cart_item;
                $keys_added[] = $cart_item_key;
            }
        }

        return $filtered;
    }

    /**
     * Get product pricing rules applicable to product
     *
     * Note: this must only be used in promotion tools when product is not yet in cart
     *
     * @access public
     * @param object $product
     * @param array $methods
     * @param bool $skip_cart_conditions
     * @param mixed $reference_amount_callback
     * @return array
     */
    public static function get_applicable_rules_for_product($product, $methods = null, $skip_cart_conditions = false, $reference_amount_callback = null)
    {

        // Maybe exclude products already on sale
        if (RP_WCDPD_Settings::get('product_pricing_sale_price_handling') === 'exclude' && RP_WCDPD_Product_Pricing::product_is_on_sale($product)) {
            return false;
        }

        // Get product pricing rules
        if ($rules = RP_WCDPD_Rules::get('product_pricing', array('methods' => $methods))) {

            // Get condition params from product
            $params = RP_WCDPD_Controller_Conditions::get_condition_params_from_product($product);

            // Maybe skip cart conditions
            $params['skip_cart_conditions'] = $skip_cart_conditions;

            // Get exclude rules
            $exclude_rules = RP_WCDPD_Rules::get('product_pricing', array('methods' => array('exclude')));

            // Check product against exclude rules
            if (empty($exclude_rules) || !RP_WCDPD_Controller_Conditions::exclude_item_by_rules($exclude_rules, $params)) {

                // Filter rules by conditions
                if ($rules = RP_WCDPD_Controller_Conditions::filter_objects($rules, $params)) {

                    // Filter rules by exclusivity settings
                    if ($mockup_adjustments = RP_WCDPD_Rules::filter_by_exclusivity('product_pricing', RP_WCDPD_Product_Pricing::get_mockup_adjustments($rules, $product, $reference_amount_callback))) {

                        // Extract rules and return
                        return wp_list_pluck($mockup_adjustments, 'rule');
                    }
                }
            }
        }

        // No rules found
        return array();
    }

    /**
     * Get mockup adjustments for use in rule exclusivity checks
     *
     * Wraps rules into arrays and calculates reference amount if needed
     *
     * Note: this must only be used in promotion tools when product is not yet in cart
     *
     * @access public
     * @param array $rules
     * @param object $product
     * @param mixed $reference_amount_callback
     * @return array
     */
    public static function get_mockup_adjustments($rules, $product, $reference_amount_callback = null)
    {

        $adjustments = array();

        // Check if reference amount is needed
        $selection_method = RP_WCDPD_Settings::get('product_pricing_rule_selection_method');
        $calculate_reference_amount = in_array($selection_method, array('smaller_price', 'bigger_price'), true);

        // Get base amount
        if ($calculate_reference_amount) {
            $base_amount = $product->get_price('edit');
        }

        // Iterate over rules
        foreach ($rules as $rule) {

            // Wrap rule
            $adjustment = array(
                'rule' => $rule,
            );

            // Maybe calculate reference amount
            if ($calculate_reference_amount) {

                // Get reference amount callback from method if not provided
                if ($reference_amount_callback === null) {

                    // Load controller
                    $controller = RP_WCDPD_Controller_Methods_Product_Pricing::get_instance();

                    // Load method
                    if ($method = $controller->get_method_from_rule($rule)) {
                        $reference_amount_callback = array($method, 'get_reference_amount');
                    }
                }

                // Get reference amount
                if ($reference_amount_callback !== null) {
                    $adjustment['reference_amount'] = call_user_func($reference_amount_callback, $adjustment, $base_amount, 1, $product);
                }
                else {
                    $adjustment['reference_amount'] = 0.0;
                }
            }

            // Add to main array
            $adjustments[] = $adjustment;
        }

        return $adjustments;
    }

    /**
     * Safe check for is on sale
     *
     * @access public
     * @param object $product
     * @return bool
     */
    public static function product_is_on_sale($product)
    {

        // Special case
        if (RP_WCDPD_Settings::get('product_pricing_change_display_prices')) {
            return $product->is_on_sale('edit');
        }
        // Regular handling
        else {
            return $product->is_on_sale();
        }
    }

    /**
     * Apply simple product pricing rules to product price
     *
     * @access public
     * @param float $price
     * @param object $product
     * @return float
     */
    public static function apply_simple_product_pricing_rules_to_product_price($price, $product)
    {

        $controller = RP_WCDPD_Controller_Methods_Product_Pricing::get_instance();

        // Get simple product pricing rules applicable to this product
        $applicable_rules = RP_WCDPD_Product_Pricing::get_applicable_rules_for_product($product, array('simple'));

        // Apply applicable adjustments
        if (is_array($applicable_rules) && !empty($applicable_rules)) {
            foreach ($applicable_rules as $applicable_rule) {

                // Load method from rule
                if ($method = $controller->get_method_from_rule($applicable_rule)) {

                    // Generate prices array
                    $prices = RightPress_Product_Price_Breakdown::generate_prices_array($price, 1, $product);

                    // Apply adjustments to prices array
                    $prices = $method->apply_adjustment_to_prices($prices, array('rule' => $applicable_rule));

                    // Incorporate new changes for cart item
                    RightPress_Product_Price_Changes::incorporate_new_changes_for_cart_item($prices);

                    // Get price from prices array
                    $price = RightPress_Product_Price_Breakdown::get_price_from_prices_array($prices, $price, $product);
                }
            }
        }

        return $price;
    }





}

RP_WCDPD_Product_Pricing::get_instance();
