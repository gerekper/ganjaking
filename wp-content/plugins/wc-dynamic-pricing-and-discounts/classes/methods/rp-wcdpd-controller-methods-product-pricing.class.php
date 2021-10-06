<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load dependencies
if (!class_exists('RP_WCDPD_Controller_Methods')) {
    require_once('rp-wcdpd-controller-methods.class.php');
}

/**
 * Product Pricing method controller
 *
 * @class RP_WCDPD_Controller_Methods_Product_Pricing
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
class RP_WCDPD_Controller_Methods_Product_Pricing extends RP_WCDPD_Controller_Methods
{

    protected $context = 'product_pricing';

    // RightPress Product Price component hook position
    private $rightpress_hook_position = 50;

    // Store prepared second stage price changes for cart items
    protected $prepared_second_stage_price_changes_for_cart_items = null;

    // Track for which rules action rp_wcdpd_product_pricing_rule_applied_to_cart has been triggered
    protected $rule_applied_action_triggered        = array();
    protected $no_price_changes_to_apply_triggered  = false;

    // Track calls to specific pricing methods (issue #491)
    public static $call_counter = array();

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

        // Product Pricing functionality is only available in frontend (issue #549)
        if (!RightPress_Help::is_request('frontend')) {
            return;
        }

        // Base price selection
        add_filter('rightpress_product_price_cart_item_base_price_candidates', array($this, 'maybe_add_cart_item_base_price_candidate'), $this->rightpress_hook_position, 4);
        add_filter('rightpress_product_price_selected_cart_item_base_price_key', array($this, 'maybe_change_selected_cart_item_base_price_key'), $this->rightpress_hook_position, 3);

        // Prepare second stage price changes for cart items in advance
        add_action('rightpress_product_price_prepare_second_stage_cart_item_price_changes', array($this, 'prepare_second_stage_price_changes_for_cart_items'), $this->rightpress_hook_position);

        // Register cart item price changes second stage callback
        add_filter('rightpress_product_price_cart_item_price_changes_second_stage_callbacks', array($this, 'add_cart_item_price_changes_second_stage_callback'), $this->rightpress_hook_position);

        // Price changes applied to cart item
        add_action('rightpress_product_price_cart_price_set', array($this, 'price_changes_applied_to_cart_item'), $this->rightpress_hook_position, 4);

        // No price changes to apply to cart
        add_action('rightpress_product_price_cart_no_changes_to_prices', array($this, 'no_price_changes_to_apply_to_cart'), $this->rightpress_hook_position);

        // Reset product price limits before prepared cart item prices are refreshed
        add_action('rightpress_product_price_cart_before_refresh_prepared_cart_item_prices', array($this, 'reset_limits_before_refreshing_prepared_cart_item_prices'));
    }

    /**
     * Register cart item price changes second stage callback
     *
     * @access public
     * @param array $callbacks
     * @return array
     */
    public function add_cart_item_price_changes_second_stage_callback($callbacks)
    {

        // Add callback
        $callbacks['rp_wcdpd'] = array($this, 'add_second_stage_price_changes_for_cart_items');

        // Return callbacks
        return $callbacks;
    }

    /**
     * Maybe add cart item base price candidate
     *
     * @access public
     * @param float $base_price_candidates
     * @param object $product
     * @param string $cart_item_key
     * @param array $cart_item
     * @return float
     */
    public function maybe_add_cart_item_base_price_candidate($base_price_candidates, $product, $cart_item_key, $cart_item)
    {
        // Get base price candidate
        $base_price_candidate = RP_WCDPD_Pricing::get_product_base_price($product, $cart_item_key, $cart_item);

        // Get base price candidate key
        $base_price_candidate_key = RightPress_Product_Price::get_price_key($base_price_candidate);

        // Add base price candidate if it does not exist yet
        if (!isset($base_price_candidates[$base_price_candidate_key])) {
            $base_price_candidates[$base_price_candidate_key] = $base_price_candidate;
        }

        // Return base price candidates
        return $base_price_candidates;
    }

    /**
     * Maybe change selected cart item base price key
     *
     * @access public
     * @param string $selected_base_price_key
     * @param array $base_price_candidate_keys
     * @param string $cart_item_key
     * @return string
     */
    public function maybe_change_selected_cart_item_base_price_key($selected_base_price_key, $base_price_candidate_keys, $cart_item_key)
    {

        // Check if any adjustments are prepared for cart item
        if (!empty($this->prepared_second_stage_price_changes_for_cart_items) && !empty($this->prepared_second_stage_price_changes_for_cart_items[$cart_item_key])) {

            // We do not wish to change base price if all prepared adjustments make no real changes to price, e.g. are fixed price discounts or percentage discounts with zero value
            $changes_price = false;

            // Generate prices arrays to test
            $prices_arrays = array(
                RightPress_Product_Price_Breakdown::generate_prices_array(10.00, PHP_INT_MAX),
                RightPress_Product_Price_Breakdown::generate_prices_array(20.00, PHP_INT_MAX),
            );

            // Iterate over prepared adjustments
            foreach ($this->prepared_second_stage_price_changes_for_cart_items[$cart_item_key] as $cart_item_adjustment) {

                // Get method from rule
                if ($method = $this->get_method_from_rule($cart_item_adjustment['rule'])) {

                    // Iterate over prices arrays
                    foreach ($prices_arrays as $prices) {

                        // Apply adjustments to prices array
                        $prices = $method->apply_adjustment_to_prices($prices, $cart_item_adjustment);

                        // Iterate over price ranges
                        foreach ($prices['ranges'] as $price_range) {

                            // Check if price has changed
                            if (RightPress_Product_Price::prices_differ($price_range['price'], $price_range['base_price'])) {

                                // Set flag
                                $changes_price = true;

                                // Do not proceed further
                                break 3;
                            }
                        }
                    }
                }
            }

            // Check if any real price adjustments are prepared and more than one base price candidate key is available
            if ($changes_price && count($base_price_candidate_keys) > 1) {

                // Select last base price candidate key
                // Note: Currently only WCDPD adds alternative base price so we assume that it's either one base price (default) or two base prices (default and alternative)
                $selected_base_price_key = array_pop($base_price_candidate_keys);
            }
        }

        // Return selected base price key
        return $selected_base_price_key;
    }

    /**
     * Prepare second stage price changes for cart items in advance
     *
     * Note:
     * - This method can be called multiple times during request for both real cart and simulated cart during price tests
     * - This method is designed to be invoked only from RightPress_Product_Price_Changes::get_price_changes_for_cart_items()
     * - This method must run before add_second_stage_price_changes_for_cart_items()
     *
     * @access public
     * @param array $cart_items
     * @return void
     */
    public function prepare_second_stage_price_changes_for_cart_items($cart_items)
    {

        // Sort cart items by price from cheapest
        $cart_items_to_process = RightPress_Product_Price_Changes::sort_cart_items_by_price($cart_items, 'ascending', true);

        // Apply exclude rules and allow developers to exclude items
        $cart_items_to_process = apply_filters('rp_wcdpd_product_pricing_cart_items', $cart_items_to_process);

        // Maybe exclude items that are already on sale
        if (RP_WCDPD_Settings::get('product_pricing_sale_price_handling') === 'exclude') {
            $cart_items_to_process = $this->exclude_cart_items_already_on_sale($cart_items_to_process);
        }

        // Get applicable adjustments
        if (apply_filters('rp_wcdpd_process_product_pricing', true)) {
            $adjustments = $this->get_applicable_adjustments($cart_items_to_process);
        }
        else {
            $adjustments = array();
        }

        // Filter adjustments
        foreach ($adjustments as $cart_item_key => $cart_item_adjustments) {

            // Filter by rule selection method and exclusivity settings
            $cart_item_adjustments = RP_WCDPD_Rules::filter_by_exclusivity($this->context, $cart_item_adjustments);

            // Set updated cart item adjustments array
            $adjustments[$cart_item_key] = $cart_item_adjustments;
        }

        // Store prepared adjustments in memory
        $this->prepared_second_stage_price_changes_for_cart_items = $adjustments;
    }

    /**
     * Add second stage price changes for cart items
     *
     * Note:
     * - This method can be called multiple times during request for both real cart and simulated cart during price tests, in the latter case $test_cart_items is not empty
     * - This method is designed to be invoked only from RightPress_Product_Price_Changes::get_price_changes_for_cart_items()
     * - This method must run after prepare_second_stage_price_changes_for_cart_items()
     *
     * @access public
     * @param array $price_changes
     * @param array $cart_items
     * @param array $test_cart_items
     * @return array
     */
    public function add_second_stage_price_changes_for_cart_items($price_changes, $cart_items, $test_cart_items = array())
    {

        // Adjustments not prepared yet, unexpected behaviour
        if ($this->prepared_second_stage_price_changes_for_cart_items === null) {
            RightPress_Help::doing_it_wrong(__METHOD__, "Method should not be called before RP_WCDPD_Controller_Methods_Product_Pricing::prepare_second_stage_price_changes_for_cart_items().", '2.3');
            return $price_changes;
        }

        // Reference adjustments to process
        $adjustments_to_process = $this->prepared_second_stage_price_changes_for_cart_items;

        // Prepare price changes for processing
        $price_changes_to_process = $price_changes;

        foreach ($price_changes_to_process as $cart_item_key => $cart_item_changes) {
            $price_changes_to_process[$cart_item_key]['prices_hash'] = RightPress_Help::get_hash(true, $cart_item_changes['prices']);
        }

        // Take snapshot of current limits
        RP_WCDPD_Limit_Product_Pricing::take_snapshot();

        // Take reference of current call counter
        $call_counter_before_application = RP_WCDPD_Controller_Methods_Product_Pricing::$call_counter;

        // Track iterations
        $i = 0;

        // Start loop so we can recalculate prices when post application conditions fail
        // Note for issue #320: if multiple cart items have post application condition that points to the same cart item, quantities are allowed to overlap
        while (true) {

            // Infinite loop protection
            if ($i++ >= 5) {
                break;
            }

            // Reset limits to snapshot at the beginning of each iteration
            RP_WCDPD_Limit_Product_Pricing::reset_to_snapshot();

            // Reset call counter at the beginning of each iteration
            RP_WCDPD_Controller_Methods_Product_Pricing::$call_counter = $call_counter_before_application;

            // Iterate over adjustments for cart items and apply them
            foreach ($adjustments_to_process as $cart_item_key => $cart_item_adjustments) {
                $price_changes_to_process[$cart_item_key]['prices'] = $this->apply_adjustments_to_prices_of_cart_item($price_changes[$cart_item_key]['prices'], $cart_items[$cart_item_key], $cart_item_key, $cart_item_adjustments, $test_cart_items);
            }

            // Get adjustments with failed post application conditions (if any)
            if ($adjustments_to_process = $this->get_adjustments_with_failed_post_application_conditions($this->prepared_second_stage_price_changes_for_cart_items, $price_changes_to_process, $cart_items)) {
                continue;
            }

            // This loop can only be iterated explicitly
            break;
        }

        // Move changes from prices to the main array
        foreach ($price_changes_to_process as $cart_item_key => $cart_item_changes) {

            // Check if any changes were actually made
            if ($cart_item_changes['prices_hash'] !== RightPress_Help::get_hash(true, $cart_item_changes['prices'])) {

                // Set update prices array
                $price_changes[$cart_item_key]['prices'] = $cart_item_changes['prices'];
            }
        }

        // Unset previously prepared adjustments
        $this->prepared_second_stage_price_changes_for_cart_items = null;

        // Return changes
        return $price_changes;
    }

    /**
     * Price changes applied to cart item
     *
     * Does not do anything during price tests
     *
     * @access public
     * @param float $price
     * @param string $cart_item_key
     * @param object $cart
     * @param array $price_change
     * @return void
     */
    public function price_changes_applied_to_cart_item($price, $cart_item_key, $cart, $price_change)
    {

        // Product pricing test is running
        if (RightPress_Product_Price_Test::is_running()) {
            return;
        }

        // Has own changes
        if (!empty($price_change['new_changes']['rp_wcdpd'])) {

            // Iterate over own price changes
            foreach ($price_change['new_changes']['rp_wcdpd'] as $current_change) {

                // Reference rule uid
                $rule_uid = $current_change['rule']['uid'];

                // Trigger rule applied action
                if (!in_array($rule_uid, $this->rule_applied_action_triggered, true)) {
                    do_action('rp_wcdpd_product_pricing_rule_applied_to_cart', $rule_uid, $current_change);
                    $this->rule_applied_action_triggered[] = $rule_uid;
                }
            }
        }
        // Does not have own changes
        else {

            $this->no_price_changes_to_apply_to_cart();
        }
    }

    /**
     * No price changes to apply to cart
     *
     * Does not do anything during price tests
     *
     * @access public
     * @return void
     */
    public function no_price_changes_to_apply_to_cart()
    {

        // Product pricing test is running
        if (RightPress_Product_Price_Test::is_running()) {
            return;
        }

        // Trigger nothing to apply action once per request
        if (empty($this->rule_applied_action_triggered) && !$this->no_price_changes_to_apply_triggered) {
            do_action('rp_wcdpd_product_pricing_nothing_to_apply');
            $this->no_price_changes_to_apply_triggered = true;
        }
    }

    /**
     * Apply adjustments to prices of cart item
     *
     * @access private
     * @param array $cart_item
     * @param string $cart_item_key
     * @param array $cart_item_adjustments
     * @param array $test_cart_items
     * @return array
     */
    private function apply_adjustments_to_prices_of_cart_item($prices, $cart_item, $cart_item_key, $cart_item_adjustments, $test_cart_items = array())
    {

        // Apply cart item adjustments
        foreach ($cart_item_adjustments as $rule_uid => $adjustment) {

            // Get method from rule
            if ($method = $this->get_method_from_rule($adjustment['rule'])) {

                // Sort price ranges to pointer position
                RightPress_Product_Price_Breakdown::sort_price_ranges_to_pointer_position($prices, 'rp_wcdpd');

                // Apply adjustment to prices of current cart item
                $prices = $method->apply_adjustment_to_prices($prices, $adjustment, $cart_item_key);
            }
        }

        // Return prices array
        return $prices;
    }

    /**
     * Get adjustments with failed post application conditions (if any)
     *
     * @access private
     * @param array $adjustments
     * @param array $price_changes
     * @param array $cart_items
     * @return array|bool
     */
    private function get_adjustments_with_failed_post_application_conditions($adjustments, $price_changes, $cart_items)
    {

        // Reference adjustments to process
        $adjustments_to_process = $adjustments;

        // Track if any post application conditions failed
        $failed = false;

        // Check against post application conditions
        foreach ($price_changes as $cart_item_key => $cart_item_changes) {
            if (!empty($cart_item_changes['prices']['post_application_conditions'])) {
                foreach ($cart_item_changes['prices']['post_application_conditions'] as $condition) {

                    // Paid cart items (issue #320)
                    if ($condition['method'] === 'paid_cart_items' && !empty($condition['cart_items'])) {

                        // Iterate over required paid cart items
                        foreach ($condition['cart_items'] as $paid_cart_item_key => $paid_quantity_requirement) {

                            $paid_quantity = 0;

                            // Add up all quantities of non-zero prices from prices array
                            foreach ($price_changes[$paid_cart_item_key]['prices']['ranges'] as $price_range) {
                                if ($price_range['price'] > 0.000001) {
                                    $paid_quantity += RightPress_Product_Price_Breakdown::get_price_range_quantity($price_range);
                                }
                            }

                            // Recalculate current cart item if required paid quantity of another cart item was not met
                            if ($paid_quantity < $paid_quantity_requirement) {

                                // Add failed post application condition
                                $adjustments_to_process[$cart_item_key][$condition['rule_uid']]['failed_post_application_conditions'][] = $condition;

                                // Set flag
                                $failed = true;

                                // Do not check another cart item since this condition already failed
                                break;
                            }
                        }
                    }
                }
            }
        }

        return ($failed ? $adjustments_to_process : false);
    }

    /**
     * Exclude cart items that are already on sale
     *
     * @access public
     * @param array $cart_items
     * @return array
     */
    public function exclude_cart_items_already_on_sale($cart_items)
    {

        foreach ($cart_items as $cart_item_key => $cart_item) {
            if ($cart_item['data']->is_on_sale()) {
                unset($cart_items[$cart_item_key]);
            }
        }

        return $cart_items;
    }

    /**
     * Legacy method kept to prevent fatal errors in case 3rd party developers used it in their custom code
     *
     * @access public
     * @return array
     */
    public static function get_change_set()
    {

        // Add warning
        RightPress_Help::doing_it_wrong(__METHOD__, "Method removed from WooCommerce Dynamic Pricing & Discounts and must not be used.", '2.3');

        // Return empty array
        return array();
    }

    /**
     * Reset product price limits before prepared cart item prices are refreshed
     *
     * @access public
     * @return void
     */
    public function reset_limits_before_refreshing_prepared_cart_item_prices()
    {

        RP_WCDPD_Limit_Product_Pricing::reset();
    }




}

RP_WCDPD_Controller_Methods_Product_Pricing::get_instance();
