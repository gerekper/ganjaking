<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Promotion: Volume Pricing Table
 *
 * @class RP_WCDPD_Promotion_Volume_Pricing_Table
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
class RP_WCDPD_Promotion_Volume_Pricing_Table
{

    // Singleton control
    protected static $instance = false; public static function get_instance() { return self::$instance ? self::$instance : (self::$instance = new self()); }

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        // Register settings structure
        add_filter('rp_wcdpd_settings_structure', array($this, 'register_settings_structure'), 140);

        // Set up promotion tool
        add_action('init', array($this, 'set_up_promotion_tool'));
    }

    /**
     * Register settings structure
     *
     * @access public
     * @param array $settings
     * @return array
     */
    public function register_settings_structure($settings)
    {
        $settings['promo']['children']['volume_pricing_table'] = array(
            'title' => __('Volume Pricing Table', 'rp_wcdpd'),
            'info'  => __('Displays a table with potential savings that come with higher quantities purchased.', 'rp_wcdpd'),
            'children' => array(
                'promo_volume_pricing_table' => array(
                    'title'     => __('Enable', 'rp_wcdpd'),
                    'type'      => 'checkbox',
                    'default'   => '0',
                ),
                'promo_volume_pricing_table_title' => array(
                    'title'     => __('Title', 'rp_wcdpd'),
                    'type'      => 'text',
                    'default'   => __('Quantity discounts', 'rp_wcdpd'),
                    'required'  => false,
                ),
                'promo_volume_pricing_table_position' => array(
                    'title'     => __('Position', 'rp_wcdpd'),
                    'type'      => 'select',
                    'default'   => 'woocommerce_before_add_to_cart_form',
                    'required'  => true,
                    'options'   => array(
                        'woocommerce_before_add_to_cart_form'       => __('Add to cart - Before', 'rp_wcdpd'),
                        'woocommerce_after_add_to_cart_form'        => __('Add to cart - After', 'rp_wcdpd'),
                        'woocommerce_product_meta_start'            => __('Product meta - Before', 'rp_wcdpd'),
                        'woocommerce_product_meta_end'              => __('Product meta - After', 'rp_wcdpd'),
                        'woocommerce_single_product_summary'        => __('Product summary - Before', 'rp_wcdpd'),
                        'woocommerce_after_single_product_summary'  => __('Product summary - After', 'rp_wcdpd'),
                        'woocommerce_after_main_content'            => __('Page content - After', 'rp_wcdpd'),
                    ),
                ),
                'promo_volume_pricing_table_layout' => array(
                    'title'     => __('Layout', 'rp_wcdpd'),
                    'type'      => 'grouped_select',
                    'default'   => 'horizontal',
                    'required'  => true,
                    'options'   => array(
                        'inline'    => array(
                            'label'     => __('Inline', 'rp_wcdpd'),
                            'options'   => array(
                                'inline-horizontal'    => __('Inline - Horizontal', 'rp_wcdpd'),
                                'inline-vertical'      => __('Inline - Vertical', 'rp_wcdpd'),
                            ),
                        ),
                        'modal'    => array(
                            'label'     => __('Modal', 'rp_wcdpd'),
                            'options'   => array(
                                'modal-horizontal'    => __('Modal - Horizontal', 'rp_wcdpd'),
                                'modal-vertical'      => __('Modal - Vertical', 'rp_wcdpd'),
                            ),
                        ),
                    ),
                ),
                'promo_volume_pricing_table_value_to_display' => array(
                    'title'     => __('Value to display', 'rp_wcdpd'),
                    'type'      => 'select',
                    'default'   => 'price',
                    'required'  => true,
                    'options'   => array(
                        'price'                 => __('Discounted price', 'rp_wcdpd'),
                        'discount_amount'       => __('Discount amount', 'rp_wcdpd'),
                        'discount_percentage'   => __('Discount percentage', 'rp_wcdpd'),
                    ),
                ),
                'promo_volume_pricing_table_missing_ranges' => array(
                    'title'     => __('Undefined quantity handling', 'rp_wcdpd'),
                    'type'      => 'select',
                    'default'   => 'display',
                    'required'  => true,
                    'options'   => array(
                        'display'   => __('Display missing range with regular price', 'rp_wcdpd'),
                        'hide'      => __('Do not display missing range', 'rp_wcdpd'),
                    ),
                ),
                'promo_volume_pricing_table_variation_layout' => array(
                    'title'     => __('Variable product handling', 'rp_wcdpd'),
                    'type'      => 'select',
                    'default'   => 'multiple',
                    'required'  => true,
                    'options'   => array(
                        'multiple'  => __('Display individual pricing tables', 'rp_wcdpd'),
                        'single'    => __('Display one pricing table with all variations', 'rp_wcdpd'),
                    ),
                ),
            ),
        );

        return $settings;
    }

    /**
     * Set up promotion tool
     *
     * @access public
     * @return void
     */
    public function set_up_promotion_tool()
    {

        // Load includes
        require_once 'includes/functions.php';

        // Check this promotion tool is active
        if (!RP_WCDPD_Settings::get('promo_volume_pricing_table')) {
            return;
        }

        // Add hook
        add_action(RP_WCDPD_Settings::get('promo_volume_pricing_table_position'), array($this, 'maybe_display_pricing_table_hook'));
    }

    /**
     * Maybe display pricing table - WooCommerce hook callback
     *
     * @access public
     * @return void
     */
    public function maybe_display_pricing_table_hook()
    {
        global $product;

        RP_WCDPD_Promotion_Volume_Pricing_Table::maybe_display_pricing_table($product);
    }

    /**
     * Maybe display pricing table for product
     *
     * @access public
     * @param object $product
     * @return void
     */
    public static function maybe_display_pricing_table($product)
    {
        // Product invalid
        if (!is_a($product, 'WC_Product')) {
            return;
        }

        // Note: we used to return from here on is_ajax() to solve #280 but then
        // needed to make the condition more specific to make sure pricing table
        // is displayed in other Quick View modals (issue #418)
        if (is_ajax() && in_array(current_action(), array('woocommerce_before_add_to_cart_form', 'woocommerce_after_add_to_cart_form'), true) && isset($_REQUEST['action']) && $_REQUEST['action'] === 'flatsome_quickview') {
            return;
        }

        // Simple product handling
        if (!$product->is_type('variable') && !$product->is_type('variation')) {

            // Get applicable rule
            if ($rule = self::get_applicable_volume_rule($product)) {

                // Get table data
                if ($table_data = self::get_table_data($product, $rule)) {

                    // Display pricing table
                    self::display_pricing_table(array(array(
                        'product'       => $product,
                        'rule'          => $rule,
                        'table_data'    => $table_data,
                    )), $product, false);
                }
            }
        }
        // Variable product handling
        else {

            // Switch to variable product if we got product variation
            if ($product->is_type('variation')) {
                $product = wc_get_product($product->get_parent_id());
            }

            $variation_rules = array();
            $available_variations = 0;

            // Get rules for all variations
            foreach ($product->get_available_variations() as $variation_data) {

                // Load variation
                $variation = wc_get_product($variation_data['variation_id']);

                // Get rule for current variation
                if ($rule = self::get_applicable_volume_rule($variation)) {

                    // Get table data
                    if ($table_data = self::get_table_data($variation, $rule)) {
                        $available_variations++;
                    }

                    // Add to main array
                    $variation_rules[] = array(
                        'product'       => $variation,
                        'rule'          => $rule,
                        'table_data'    => $table_data,
                    );
                }
            }

            // Display table
            if (!empty($variation_rules) && $available_variations) {
                self::display_pricing_table($variation_rules, $product, true);
            }
        }
    }

    /**
     * Check if product variation prices match
     *
     * @access public
     * @param array $data
     * @return bool
     */
    public static function variation_prices_match($data)
    {
        $last = null;

        // Check each variation
        foreach ($data as $single) {

            $current = 0.0;

            // Variation not available
            if ($single['table_data'] === false) {
                return false;
            }

            // Add prices of all ranges
            foreach ($single['table_data'] as $range) {
                $current += (float) $range['price_raw'];
            }

            // Prices do not match
            if ($last !== null && $last !== $current) {
                return false;
            }

            $last = $current;
        }

        return true;
    }

    /**
     * Check if rule applies to all variations
     *
     * @access public
     * @param array $data
     * @return bool
     */
    public static function rule_always_applies_to_all_variations($data)
    {
        // Check each variation
        foreach ($data as $single) {
            if (!empty($single['rule']['conditions'])) {
                foreach ($single['rule']['conditions'] as $condition) {
                    if (RP_WCDPD_Controller_Conditions::is_type($condition, array('product__variation', 'product__attributes', 'product_property__regular_price', 'product_property__on_sale', 'product_property__stock_quantity', 'product_property__meta'))) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Display volume pricing table
     *
     * @access public
     * @param array $data
     * @param object $product
     * @param bool $is_variable
     * @return void
     */
    public static function display_pricing_table($data, $product, $is_variable)
    {
        // Allow developers to abort
        if (!apply_filters('rp_wcdpd_volume_pricing_table_display', true, $data, $product, $is_variable)) {
            return;
        }

        // Get table layout
        $layout = RP_WCDPD_Settings::get('promo_volume_pricing_table_layout');

        // Get table title
        $title = apply_filters('rp_wcdpd_volume_pricing_table_title', RP_WCDPD_Settings::get('promo_volume_pricing_table_title'), $product, $data, $is_variable);

        // Display one pricing table for simple product or variable product with equal prices
        if (!$is_variable || (self::variation_prices_match($data) && self::rule_always_applies_to_all_variations($data) && RP_WCDPD_Settings::get('promo_volume_pricing_table_variation_layout') === 'multiple')) {

            // Treat multiple variations as one since prices are the same
            $single = array_shift($data);
            $is_variable = false;

            // Display table
            RightPress_Help::include_extension_template('promotion-volume-pricing-table', $layout, RP_WCDPD_PLUGIN_PATH, RP_WCDPD_PLUGIN_KEY, array('title' => $title, 'data' => array($single)));
        }
        // Display one table for all variations
        else if (RP_WCDPD_Settings::get('promo_volume_pricing_table_variation_layout') === 'single') {

            // Display table
            RightPress_Help::include_extension_template('promotion-volume-pricing-table', $layout, RP_WCDPD_PLUGIN_PATH, RP_WCDPD_PLUGIN_KEY, array('title' => $title, 'data' => $data));
        }
        // Display multiple individual tables for multiple variations
        else {

            // Open variable product container
            echo '<div id="rp_wcdpd_pricing_table_variation_container" class="rp_wcdpd_pricing_table_variation_container">';

            // Iterate over products with rules
            foreach ($data as $single) {

                // Current variation is unavailable - do not display a table for it
                if ($single['table_data'] === false) {
                    continue;
                }

                // Open variation container
                echo '<div id="rp_wcdpd_pricing_table_variation_' . $single['product']->get_id() . '" class="rp_wcdpd_pricing_table_variation">';

                // Display table
                RightPress_Help::include_extension_template('promotion-volume-pricing-table', $layout, RP_WCDPD_PLUGIN_PATH, RP_WCDPD_PLUGIN_KEY, array('title' => $title, 'data' => array($single)));

                // Close variation container
                echo '</div>';
            }

            // Close variable product container
            echo '</div>';
        }

        // Inject styles
        RightPress_Help::inject_stylesheet('rp-wcdpd-promotion-volume-pricing-table-styles', RP_WCDPD_PLUGIN_URL . '/extensions/promotion-volume-pricing-table/assets/styles.css', RP_WCDPD_VERSION);

        // Inject script in place for Ajax requests (normally Product Quick View type of requests)
        if (is_ajax()) {
            echo '<script type="text/javascript" src="' . RP_WCDPD_PLUGIN_URL . '/extensions/promotion-volume-pricing-table/assets/scripts.js"></script>';
        }
        // Enqueue scripts the regular way
        else {
            wp_enqueue_script('rp-wcdpd-promotion-volume-pricing-table-scripts', RP_WCDPD_PLUGIN_URL . '/extensions/promotion-volume-pricing-table/assets/scripts.js', array('jquery'), RP_WCDPD_VERSION);
        }
    }

    /**
     * Get applicable volume rule
     *
     * Note: This feature assumes that considering all conditions only one
     * volume rule will be applicable to one product. If there are more than
     * one volume rule, the first one in a row will be selected.
     *
     * @access public
     * @param object $product
     * @return array|bool
     */
    public static function get_applicable_volume_rule($product)
    {
        // Get matching rules
        if ($matched_rules = RP_WCDPD_Product_Pricing::get_applicable_rules_for_product($product, array('bulk', 'tiered'), false, array('RP_WCDPD_Promotion_Volume_Pricing_Table', 'get_reference_amount_for_table'))) {

            // Get applicable rule
            $applicable_rules = array_values($matched_rules);
            $applicable_rule = array_shift($applicable_rules);

            // Allow developers to override and return
            return apply_filters('rp_wcdpd_volume_pricing_table_applicable_rule', $applicable_rule, $matched_rules, $product);
        }

        return false;
    }

    /**
     * Get reference amount for pricing table
     *
     * Note: currently this simply calculates the max discount that a rule gives,
     * it disregards quantity of items at that discount as well as discounts
     * provided in other quantity ranges (i.e. those with smaller discounts)
     *
     * @access public
     * @param array $adjustment
     * @param float $base_amount
     * @return float
     */
    public static function get_reference_amount_for_table($adjustment, $base_amount)
    {
        $amount = 0;

        // Check if at least one quantity range is defined
        if (!empty($adjustment['rule']['quantity_ranges'])) {

            // Store all adjustments
            $adjustments = array();

            // Iterate over quantity ranges
            foreach ($adjustment['rule']['quantity_ranges'] as $quantity_range) {

                // Get adjustment value
                $adjustment = RP_WCDPD_Pricing::get_adjustment_value($quantity_range['pricing_method'], $quantity_range['pricing_value'], $base_amount);
                $adjustments[] = abs($adjustment);
            }

            // Get max adjustment value
            $amount = max($adjustments);
        }

        return (float) $amount;
    }

    /**
     * Get table data
     *
     * @access public
     * @param array $product
     * @param array $rule
     * @return array|false
     */
    public static function get_table_data($product, $rule)
    {
        $data = array();

        // Set flag
        RightPress_Product_Price_Test::test_started();

        // Get product price
        $regular_price = $product->get_regular_price();
        $final_price = $product->get_price();

        // Remove flag
        RightPress_Product_Price_Test::test_ended();

        // Get quantity ranges
        $quantity_ranges = $rule['quantity_ranges'];

        // Maybe add missing ranges
        if (RP_WCDPD_Settings::get('promo_volume_pricing_table_missing_ranges') === 'display') {
            $quantity_ranges = RP_WCDPD_Pricing::add_volume_pricing_rule_missing_quantity_ranges($quantity_ranges);
        }

        // Get data for each quantity range
        foreach ($quantity_ranges as $quantity_range) {

            // Get from and to quantities
            $from = $quantity_range['from'];
            $to = $quantity_range['to'];

            // Format range label
            if ($from === $to) {
                $label = $from;
            }
            else if (empty($to)) {
                $label = $from . '+';
            }
            else {
                $label = $from . '-' . $to;
            }

            // Select correct price to adjust
            $price_to_adjust = (RP_WCDPD_Settings::get('product_pricing_sale_price_handling') === 'regular' && empty($quantity_range['is_missing_range'])) ? $regular_price : $final_price;

            // Price is not set in product settings
            if ($price_to_adjust === '') {
                return false;
            }

            // realmag777 currency switcher compatibility
            $pricing_value = apply_filters('woocs_convert_price_wcdp', $quantity_range['pricing_value'], false, $quantity_range['pricing_method']);

            // Calculate price
            $price = RP_WCDPD_Pricing::adjust_amount($price_to_adjust, $quantity_range['pricing_method'], $pricing_value);

            // Display discount amount
            if (RP_WCDPD_Settings::get('promo_volume_pricing_table_value_to_display') === 'discount_amount') {
                $raw_value = $price - $price_to_adjust;
                $sign = $raw_value <= 0 ? '-' : '+';
                $display_value = apply_filters('rp_wcdpd_volume_pricing_table_range_discount_amount', ($sign . wc_price(wc_get_price_to_display($product, array('qty' => 1, 'price' => abs($raw_value))))), $product, $rule, $quantity_range, $price, $raw_value);
            }
            // Display discount percentage
            else if (RP_WCDPD_Settings::get('promo_volume_pricing_table_value_to_display') === 'discount_percentage') {
                $raw_value = 100 - ($price / $price_to_adjust) * 100;
                $sign = $raw_value >= 0 ? '-' : '+';
                $display_value = apply_filters('rp_wcdpd_volume_pricing_table_range_discount_percentage', ($sign . round($raw_value, 2) . '%'), $product, $rule, $quantity_range, $raw_value);
            }
            // Display discounted price (separate filter maintained for backwards compatibility before issue #454 was fixed)
            else {
                $display_value = apply_filters('rp_wcdpd_volume_pricing_table_range_price', wc_price(wc_get_price_to_display($product, array('qty' => 1, 'price' => $price))), $product, $rule, $quantity_range, $price);
                $raw_value = $price;
            }

            // Add to array
            $data[] = array(
                'range_label'   => apply_filters('rp_wcdpd_volume_pricing_table_range_label', $label, $product, $rule, $quantity_range),
                'range_price'   => apply_filters('rp_wcdpd_volume_pricing_table_range_value', $display_value, $raw_value, $product, $rule, $quantity_range),
                'price_raw'     => $raw_value,
                'from'          => $from,
            );
        }

        // Allow developers to make changes and return
        return apply_filters('rp_wcdpd_volume_pricing_table_data', $data, $product, $rule);
    }

    /**
     * Select quantity range by quantity
     *
     * @access public
     * @param array $quantity_ranges
     * @param int $quantity
     * @return array|bool
     */
    public static function select_quantity_range_by_quantity($quantity_ranges, $quantity)
    {
        foreach ($quantity_ranges as $quantity_range) {
            if ($quantity_range['from'] <= $quantity && $quantity <= $quantity_range['to']) {
                return $quantity_range;
            }
        }

        return false;
    }

    /**
     * Get variation attributes string
     *
     * @access public
     * @param object $product
     * @return string
     */
    public static function get_variation_attributes_string($product)
    {
        // Product is not variation
        if (!$product->is_type('variation')) {
            return '';
        }

        // Get variation attributes
        $attributes = $product->get_variation_attributes();

        // Convert to string and return
        return http_build_query($attributes);
    }






}

RP_WCDPD_Promotion_Volume_Pricing_Table::get_instance();
