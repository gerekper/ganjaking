<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Product Price Override In Shop
 *
 * @class RP_WCDPD_Product_Price_Shop
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
class RP_WCDPD_Product_Price_Shop
{

    // Singleton control
    protected static $instance = false; public static function get_instance() { return self::$instance ? self::$instance : (self::$instance = new self()); }

    // RightPress Product Price component hook position
    private $rightpress_hook_position = 50;

    private $skip_cache = null;

    private $rules = null;

    private $product_condition_values = array();

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        // Set up on init so that we have access to settings
        add_action('init', array($this, 'init'));
    }

    /**
     * Set up on init
     *
     * @access public
     * @return void
     */
    public function init()
    {

        // Prices do not need to be changed in shop
        if (!RP_WCDPD_Settings::get('product_pricing_change_display_prices')) {
            return;
        }

        // No pricing rules configured
        if (!$this->get_rules()) {
            return;
        }

        // Base price selection
        add_filter('rightpress_product_price_shop_base_price_candidates', array($this, 'maybe_add_shop_base_price_candidate'), $this->rightpress_hook_position, 4);
        add_filter('rightpress_product_price_selected_shop_base_price_key', array($this, 'maybe_change_selected_shop_base_price_key'), $this->rightpress_hook_position, 2);

        // Maybe force calculation by price test
        add_filter('rightpress_product_price_shop_calculate_by_price_test', array($this, 'maybe_force_calculation_by_price_test'), $this->rightpress_hook_position, 4);

        // Add product shop price calculation callback
        add_filter('rightpress_product_price_shop_calculation_callbacks', array($this, 'add_calculation_callback'), $this->rightpress_hook_position);

        // Add cache hash data
        add_filter('rightpress_product_price_shop_cache_hash_data', array($this, 'add_cache_hash_data'), $this->rightpress_hook_position, 4);

        // Add settings hash data
        add_filter('rightpress_product_price_shop_settings_hash_data', array($this, 'add_settings_hash_data'), $this->rightpress_hook_position, 2);

        // Maybe skip cache for this request
        add_filter('rightpress_product_price_shop_skip_cache', array($this, 'maybe_skip_cache'), $this->rightpress_hook_position, 2);

        // Maybe change cache expiration timestamp
        add_filter('rightpress_product_price_shop_cache_record_expiration_timestamp', array($this, 'maybe_change_cache_expiration_timestamp'), $this->rightpress_hook_position, 5);

        // Maybe change prices in the backend
        add_filter('rightpress_product_price_shop_change_prices_in_backend', array($this, 'maybe_change_prices_in_backend'), $this->rightpress_hook_position, 4);
    }

    /**
     * Add product shop price calculation callback
     *
     * @access public
     * @param array $callbacks
     * @return array
     */
    public function add_calculation_callback($callbacks)
    {

        // Add callback
        $callbacks['rp_wcdpd'] = array($this, 'calculate_price');

        // Return list of callbacks
        return $callbacks;
    }

    /**
     * Maybe add shop base price candidate
     *
     * @access public
     * @param float $base_price_candidates
     * @param float $price
     * @param string $price_type
     * @param object $product
     * @return float
     */
    public function maybe_add_shop_base_price_candidate($base_price_candidates, $price, $price_type, $product)
    {

        // Check if base price needs to be changed
        if ($price_type === 'price' && RP_WCDPD_Settings::get('product_pricing_sale_price_handling') === 'regular') {

            // Start price observation
            RightPress_Product_Price_Shop::start_observation();

            // Get product id
            $product_id = $product->get_id();

            // Run price methods to observe prices
            $product->get_sale_price();
            $product->get_regular_price();

            // Get observed prices
            $observed_prices = RightPress_Product_Price_Shop::get_observed();

            // Extract observed prices
            $sale_price    = $observed_prices[$product_id]['sale_price'];
            $regular_price = $observed_prices[$product_id]['regular_price'];

            // Make sure product is on sale and sale price is lower than regular price
            if ($sale_price !== '' && RightPress_Product_Price::price_is_smaller_than($sale_price, $regular_price)) {

                // Get base price candidate key
                $base_price_candidate_key = RightPress_Product_Price::get_price_key($regular_price);

                // Add base price candidate if it does not exist yet
                if (!isset($base_price_candidates[$base_price_candidate_key])) {
                    $base_price_candidates[$base_price_candidate_key] = $regular_price;
                }
            }
        }

        // Return base price candidates
        return $base_price_candidates;
    }

    /**
     * Maybe change selected shop base price key
     *
     * @access public
     * @param string $selected_base_price_key
     * @param array $calculation_data
     * @return string
     */
    public function maybe_change_selected_shop_base_price_key($selected_base_price_key, $calculation_data)
    {

        // Check if more than one base price candidate key is available
        if (count($calculation_data['alternatives']) > 1) {

            // Check if any adjustments were made by the plugin
            if (!empty($calculation_data['changes']['rp_wcdpd'])) {

                // Select last base price candidate key
                $selected_base_price_key = $this->get_last_alternative_key($calculation_data['alternatives']);
            }
        }

        // Return selected base price key
        return $selected_base_price_key;
    }

    /**
     * Get last alternative key from a list of calculation data alternatives
     *
     * Note: Currently only WCDPD adds alternative base price so we assume that it's either
     * one base price (default) or two base prices (default and alternative)
     *
     * @access public
     * @param array $calculation_data_alternatives
     * @return string
     */
    public function get_last_alternative_key($calculation_data_alternatives)
    {

        // Get all keys
        $keys = array_keys($calculation_data_alternatives);

        // Return last key
        return array_pop($keys);
    }

    /**
     * Maybe force calculation by price test
     *
     * @access public
     * @param bool $calculate_by_price_test
     * @param float $price
     * @param string $price_type
     * @param object $product
     * @return bool
     */
    public function maybe_force_calculation_by_price_test($calculate_by_price_test, $price, $price_type, $product)
    {

        // Check setting
        if (RP_WCDPD_Settings::get('product_pricing_change_display_prices') === 'change_all') {

            $calculate_by_price_test = true;
        }

        return $calculate_by_price_test;
    }

    /**
     * Calculate price
     *
     * Note: We only work with the last alternative since if this plugin will make any changes, the last alternative
     * will be used by default (currently no other plugin but WCDPD adds alternative base price keys)
     *
     * @access public
     * @param array $calculation_data
     * @param string $price_type
     * @param object $product
     * @return array
     */
    public function calculate_price($calculation_data, $price_type, $product)
    {

        // Maybe skip plugin-specific calculation
        if ($this->skip_calculation($product)) {
            return $calculation_data;
        }

        // Get last base price candidate key
        $base_price_candidate_key = $this->get_last_alternative_key($calculation_data['alternatives']);

        // Reference corresponding alternative data array
        $alternative_data = $calculation_data['alternatives'][$base_price_candidate_key];

        // Reference current price
        // Note: At this point in some cases 'price' can be an empty string, e.g. no sale price set in product settings
        $adjusted_price = $alternative_data['price'];

        // Calculate price
        if ($price_type === 'price') {

            // Get adjusted price
            $adjusted_price = $this->get_adjusted_price($adjusted_price, $product);
        }
        // Calculate sale price
        else if ($price_type === 'sale_price') {

            // Get final and regular prices
            $final_price    = (float) $product->get_price();
            $regular_price  = (float) $product->get_regular_price();

            // Product is considered to have a sale price if its final price is lower than regular price
            if (RightPress_Product_Price::price_is_smaller_than($final_price, $regular_price)) {

                $adjusted_price = $final_price;
            }
            // Empty sale price
            else {

                throw new RightPress_Product_Price_Exception('empty_price', 'Empty price.');
            }
        }
        // Calculate regular price
        else if ($price_type === 'regular_price') {

            // Regular price does not need to be displayed
            if (!RP_WCDPD_Settings::get('product_pricing_display_regular_price')) {

                // Set regular price to final price
                $adjusted_price = $product->get_price();
            }
        }

        // Check if price was adjusted
        if (RightPress_Product_Price::prices_differ($adjusted_price, $alternative_data['price'])) {

                // Set new price
                $calculation_data['alternatives'][$base_price_candidate_key]['price'] = $adjusted_price;

                // Add change data
                $calculation_data['changes']['rp_wcdpd'][RightPress_Help::get_hash()] = array();
        }

        // Return calculation data
        return $calculation_data;
    }

    /**
     * Maybe skip price calculation
     *
     * @access public
     * @param object $product
     * @return bool
     */
    public function skip_calculation($product)
    {

        // Product pricing test in progress
        if (RightPress_Product_Price_Test::is_running()) {
            return true;
        }

        // Do not skip
        return false;
    }

    /**
     * Get adjusted price
     *
     * @access public
     * @param float $price
     * @param object $product
     * @return float
     */
    public function get_adjusted_price($price, $product)
    {

        // Change display prices by simple rules
        if (RP_WCDPD_Settings::get('product_pricing_change_display_prices') === 'change_simple') {
            $price = RP_WCDPD_Product_Pricing::apply_simple_product_pricing_rules_to_product_price($price, $product);
        }

        return $price;
    }

    /**
     * Add cache hash data
     *
     * @access public
     * @param array $hash_data
     * @param float $price
     * @param string $price_type
     * @param object $product
     * @return array
     */
    public function add_cache_hash_data($hash_data, $price, $price_type, $product)
    {

        // Add product condition values
        $hash_data['rp_wcdpd'] = array(
            $this->get_product_condition_values($product),
        );

        // Return hash data
        return $hash_data;
    }

    /**
     * Add settings hash data
     *
     * @access public
     * @param array $hash_data
     * @param object $deprecated_1
     * @return array
     */
    public function add_settings_hash_data($hash_data, $deprecated_1)
    {

        // Add data
        $hash_data['rp_wcdpd'] = array(
            $this->get_rules(),
            RP_WCDPD_Settings::get('product_pricing_rule_selection_method'),
            RP_WCDPD_Settings::get('product_pricing_sale_price_handling'),
            RP_WCDPD_Settings::get('product_pricing_change_display_prices'),
            RP_WCDPD_Settings::get('product_pricing_display_regular_price'),
            RP_WCDPD_Settings::get('condition_amounts_include_tax'),
        );

        // Return hash data
        return $hash_data;
    }

    /**
     * Maybe skip cache
     *
     * @access public
     * @param bool $skip
     * @param object $product
     * @return bool
     */
    public function maybe_skip_cache($skip, $product)
    {

        // Cache already skipped by another plugin
        if ($skip) {
            return $skip;
        }

        // Skip for this call only
        if ($this->skip_calculation($product)) {
            return true;
        }

        // We have not checked this yet for current request
        if ($this->skip_cache === null) {

            $this->skip_cache = false;

            // Pricing rules contain customer conditions and customer is logged in
            if (RP_WCDPD_Rules::rules_have_condition_groups(array('product_pricing'), array('customer', 'customer_value', 'purchase_history', 'purchase_history_quantity', 'purchase_history_value')) && is_user_logged_in()) {
                $this->skip_cache = true;
            }
            // Pricing rules contain cart conditions and cart is not empty
            else if (RP_WCDPD_Rules::rules_have_condition_groups(array('product_pricing'), array('cart', 'cart_items', 'cart_item_quantities', 'cart_item_subtotals', 'checkout', 'shipping')) && RightPress_Help::get_wc_cart_item_count()) {
                $this->skip_cache = true;
            }
            // All rule types are considered for price overrides, at least one non-simple non-disabled rule is configured and cart is not empty
            else if (RP_WCDPD_Settings::get('product_pricing_change_display_prices') === 'change_all' && RP_WCDPD_Rules::get('product_pricing', array('methods' => array('bulk', 'tiered', 'group', 'group_repeat', 'bogo_xx', 'bogo_xx_repeat', 'bogo', 'bogo_repeat'))) && RightPress_Help::get_wc_cart_item_count()) {
                $this->skip_cache = true;
            }
        }

        // Skip for the entire request
        return $this->skip_cache;
    }

    /**
     * Maybe change cache expiration timestamp
     *
     * @access public
     * @param int $timestamp
     * @param float $price
     * @param string $price_type
     * @param WC_Product $product
     * @param string $price_hash
     * @return int
     */
    public function maybe_change_cache_expiration_timestamp($timestamp, $price, $price_type, $product, $price_hash)
    {

        $condition_timestamps = array();

        // Iterate over rules
        foreach ($this->get_rules() as $rule) {

            // Iterate over conditions
            if (!empty($rule['conditions'])) {
                foreach ($rule['conditions'] as $rule_condition) {

                    // Check if condition is time related condition
                    if (!RP_WCDPD_Controller_Conditions::is_group($rule_condition, array('time'))) {
                        continue;
                    }

                    // Get condition
                    if ($condition = RP_WCDPD_Controller_Conditions::get_item($rule_condition['type'])) {

                        // Get condition value
                        $condition_value = $condition->get_condition_value(array('condition' => $rule_condition));

                        // Condition type support is not implemented
                        // Note: If we are to add new time-related condition, we would need to reflect that in the code block below
                        if (!in_array($condition->get_key(), array('date', 'time', 'datetime', 'weekdays'), true)) {
                            // TODO: Maybe write to log?
                            continue;
                        }

                        // Get condition method
                        if ($method = RP_WCDPD_Controller_Conditions::get_instance()->get_condition_methods_controller()->get_item($condition->get_method())) {

                            // Get current datetime
                            $current_datetime = RightPress_Help::get_datetime_object();

                            // Day of week
                            if ($condition->get_key() === 'weekdays') {

                                // Get current weekday
                                $current_weekday = $current_datetime->format('w');

                                // Check if current weekday is selected
                                $current_weekday_is_selected = in_array($current_weekday, $condition_value, true);

                                // Format list of weekdays of two weeks
                                $weekdays = array('0', '1', '2', '3', '4', '5', '6', '0', '1', '2', '3', '4', '5', '6');

                                // Remove weekdays that have passed
                                $weekdays = array_slice($weekdays, array_search($current_weekday, $weekdays, true));

                                // Track last processed weekday if we needed to go back to it
                                $last_processed = null;

                                // Iterate over weekdays starting from current weekday
                                foreach ($weekdays as $weekday) {

                                    // If current weekday is selected, we are searching for the next non-selected weekday
                                    if ($current_weekday_is_selected && !in_array($weekday, $condition_value, true)) {

                                        // Rule is valid till the end of the last processed weekday
                                        $datetime = RightPress_Help::get_datetime_object(RightPress_Help::get_weekday_names()[intval($last_processed)], false)->setTime(23, 59, 59);

                                        // Format timestamp and add to array
                                        $condition_timestamps[] = $datetime->getTimestamp();

                                        // Do not proceed to the next weekday
                                        break;
                                    }
                                    // If current weekday is not selected, we are searching for the next selected weekday
                                    else if (!$current_weekday_is_selected && in_array($weekday, $condition_value, true)) {

                                        // Rule is valid from the start of the weekday that is currently processed
                                        $datetime = RightPress_Help::get_datetime_object(RightPress_Help::get_weekday_names()[intval($weekday)], false);

                                        // Format timestamp and add to array
                                        $condition_timestamps[] = $datetime->getTimestamp();

                                        // Do not proceed to the next weekday
                                        break;
                                    }

                                    // Set last processed weekday
                                    $last_processed = $weekday;
                                }
                            }
                            // Time
                            else if ($condition->get_key() === 'time') {

                                // Get condition value datetime
                                $datetime = $method->get_datetime($rule_condition['method_option'], $condition_value);

                                // Switch to another day if today this time has passed
                                if ($datetime->format('His') < $current_datetime->format('His')) {
                                    $datetime->modify('+1 day');
                                }

                                // Format timestamp and add to array
                                $condition_timestamps[] = $datetime->getTimestamp();
                            }
                            // Date or datetime
                            else {

                                // Get condition value datetime
                                $datetime = $method->get_datetime($rule_condition['method_option'], $condition_value);

                                // Set time to 23:59:59 for date condition under specific circumstances
                                if ($condition->get_key() === 'date' && ($rule_condition['method_option'] === 'to' || ($rule_condition['method_option'] === 'specific_date' && $datetime->format('Y-m-d') === $current_datetime->format('Y-m-d')))) {
                                    $datetime->setTime(23, 59, 59);
                                }

                                // Format timestamp and add to array if datetime is still in the future
                                if ($datetime > $current_datetime) {
                                    $condition_timestamps[] = $datetime->getTimestamp();
                                }
                            }
                        }
                    }
                }
            }
        }

        // Maybe change cache expiration timestamp to the next scheduled rule validity change timestamp
        if (!empty($condition_timestamps) && min($condition_timestamps) < $timestamp) {
            $timestamp = min($condition_timestamps);
        }

        return $timestamp;
    }

    /**
     * Maybe change prices in the backend
     *
     * @access public
     * @param bool $change
     * @param float $price
     * @param string $price_type
     * @param object $product
     * @return bool
     */
    public function maybe_change_prices_in_backend($change, $price, $price_type, $product)
    {

        if (!$change) {
            $change = apply_filters('rp_wcdpd_allow_backend_price_override', false);
        }

        return $change;
    }

    /**
     * Get values for all product conditions for all rules
     *
     * @access public
     * @param object $product
     * @return array
     */
    public function get_product_condition_values($product)
    {

        // Get product id
        $product_id = $product->get_id();

        // Get values and store in cache
        if (!isset($this->product_condition_values[$product_id])) {

            $this->product_condition_values[$product_id] = array();

            // Get condition params from product
            $params = RP_WCDPD_Controller_Conditions::get_condition_params_from_product($product);

            $processed = array();

            // Iterate over rules
            foreach ($this->get_rules() as $rule) {

                // Iterate over conditions
                if (!empty($rule['conditions'])) {
                    foreach ($rule['conditions'] as $rule_condition) {

                        // Check if condition is product condition
                        if (RP_WCDPD_Controller_Conditions::is_group($rule_condition, array('product', 'product_property', 'product_other', 'custom_taxonomy'))) {

                            // Get condition value
                            if ($condition = RP_WCDPD_Controller_Conditions::get_item($rule_condition['type'])) {
                                $this->product_condition_values[$product_id][][$rule_condition['type']] = $condition->get_value(array_merge($params, array('condition' => $rule_condition)));
                            }
                        }
                    }
                }
            }
        }

        // Return from cache
        return $this->product_condition_values[$product_id];
    }

    /**
     * Get rules
     *
     * @access public
     * @return array
     */
    public function get_rules()
    {

        // Rules not loaded yet
        if ($this->rules === null) {

            $params = array();

            // Simple rules only
            if (RP_WCDPD_Settings::get('product_pricing_change_display_prices') === 'change_simple') {
                $params['methods'] = array('simple');
            }

            // Load rules
            $this->rules = RP_WCDPD_Rules::get('product_pricing', $params);
        }

        return $this->rules;
    }





}

RP_WCDPD_Product_Price_Shop::get_instance();
