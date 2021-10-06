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

        // Listen for Ajax calls
        add_action('wp_ajax_rp_wcdpd_load_variation_pricing_table', array($this, 'load_variation_pricing_table'));
        add_action('wp_ajax_nopriv_rp_wcdpd_load_variation_pricing_table', array($this, 'load_variation_pricing_table'));
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
            'title' => esc_html__('Volume Pricing Table', 'rp_wcdpd'),
            'info'  => esc_html__('Displays a table with potential savings that come with higher quantities purchased.', 'rp_wcdpd'),
            'children' => array(
                'promo_volume_pricing_table' => array(
                    'title'     => esc_html__('Enable', 'rp_wcdpd'),
                    'type'      => 'checkbox',
                    'default'   => '0',
                ),
                'promo_volume_pricing_table_title' => array(
                    'title'     => esc_html__('Title', 'rp_wcdpd'),
                    'type'      => 'text',
                    'default'   => esc_html__('Quantity discounts', 'rp_wcdpd'),
                    'required'  => false,
                ),
                'promo_volume_pricing_table_position' => array(
                    'title'     => esc_html__('Position', 'rp_wcdpd'),
                    'type'      => 'select',
                    'default'   => 'woocommerce_before_add_to_cart_form',
                    'required'  => true,
                    'options'   => array(
                        'woocommerce_before_add_to_cart_form'       => esc_html__('Add to cart - Before', 'rp_wcdpd'),
                        'woocommerce_after_add_to_cart_form'        => esc_html__('Add to cart - After', 'rp_wcdpd'),
                        'woocommerce_product_meta_start'            => esc_html__('Product meta - Before', 'rp_wcdpd'),
                        'woocommerce_product_meta_end'              => esc_html__('Product meta - After', 'rp_wcdpd'),
                        'woocommerce_single_product_summary'        => esc_html__('Product summary - Before', 'rp_wcdpd'),
                        'woocommerce_after_single_product_summary'  => esc_html__('Product summary - After', 'rp_wcdpd'),
                    ),
                ),
                'promo_volume_pricing_table_layout' => array(
                    'title'     => esc_html__('Layout', 'rp_wcdpd'),
                    'type'      => 'grouped_select',
                    'default'   => 'horizontal',
                    'required'  => true,
                    'options'   => array(
                        'inline'    => array(
                            'label'     => esc_html__('Inline', 'rp_wcdpd'),
                            'options'   => array(
                                'inline-horizontal'    => esc_html__('Inline - Horizontal', 'rp_wcdpd'),
                                'inline-vertical'      => esc_html__('Inline - Vertical', 'rp_wcdpd'),
                            ),
                        ),
                        'modal'    => array(
                            'label'     => esc_html__('Modal', 'rp_wcdpd'),
                            'options'   => array(
                                'modal-horizontal'    => esc_html__('Modal - Horizontal', 'rp_wcdpd'),
                                'modal-vertical'      => esc_html__('Modal - Vertical', 'rp_wcdpd'),
                            ),
                        ),
                    ),
                ),
                'promo_volume_pricing_table_value_to_display' => array(
                    'title'     => esc_html__('Value to display', 'rp_wcdpd'),
                    'type'      => 'select',
                    'default'   => 'price',
                    'required'  => true,
                    'options'   => array(
                        'price'                 => esc_html__('Discounted price', 'rp_wcdpd'),
                        'discount_amount'       => esc_html__('Discount amount', 'rp_wcdpd'),
                        'discount_percentage'   => esc_html__('Discount percentage', 'rp_wcdpd'),
                    ),
                ),
                'promo_volume_pricing_table_missing_ranges' => array(
                    'title'     => esc_html__('Undefined quantity handling', 'rp_wcdpd'),
                    'type'      => 'select',
                    'default'   => 'display',
                    'required'  => true,
                    'options'   => array(
                        'display'   => esc_html__('Display missing range with regular price', 'rp_wcdpd'),
                        'hide'      => esc_html__('Do not display missing range', 'rp_wcdpd'),
                    ),
                ),
                'promo_volume_pricing_table_variation_layout' => array(
                    'title'     => esc_html__('Variable product handling', 'rp_wcdpd'),
                    'type'      => 'select',
                    'default'   => 'multiple',
                    'required'  => true,
                    'options'   => array(
                        'multiple'  => esc_html__('Display individual pricing tables', 'rp_wcdpd'),
                        'single'    => esc_html__('Display one pricing table with all variations', 'rp_wcdpd'),
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

        // Get position hook
        $position = RP_WCDPD_Settings::get('promo_volume_pricing_table_position');

        // "Page content - After" no longer available since version 2.4 (related to issue #457), switch to default
        if ($position === 'woocommerce_after_main_content') {
            $position = 'woocommerce_before_add_to_cart_form';
        }

        // Add hook
        add_action($position, array($this, 'maybe_display_pricing_table_hook'));
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

        $is_displayed = false;

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

        // Do not display dynamically loaded variation pricing tables during Ajax requests as they wouldn't work here
        if (is_ajax() && $product->is_type(array('variable', 'variation')) && RP_WCDPD_Settings::get('promo_volume_pricing_table_variation_layout') === 'multiple') {
            return;
        }

        // Simple product handling
        if (!$product->is_type(array('variable', 'variation'))) {

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

                    // Set flag
                    $is_displayed = true;
                }
            }
        }
        // Variable product handling
        else {

            // Switch to variable product if we got product variation
            if ($product->is_type('variation')) {
                $product = wc_get_product($product->get_parent_id());
            }

            // Display single table for all variations
            if (RP_WCDPD_Settings::get('promo_volume_pricing_table_variation_layout') === 'single') {

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

                // Check if we have something to display
                if (!empty($variation_rules) && $available_variations) {

                    // Display pricing table
                    self::display_pricing_table($variation_rules, $product, true);

                    // Set flag
                    $is_displayed = true;
                }
            }
            // Display individual tables for variations
            else {

                // Display container
                RP_WCDPD_Promotion_Volume_Pricing_Table::display_pricing_table_variation_container();

                // Set flag
                $is_displayed = true;
            }
        }

        // Check if pricing table is displayed
        if ($is_displayed) {

            // Load jQuery plugins
            RightPress_Loader::load_jquery_plugin('rightpress-helper');
            RightPress_Loader::load_jquery_plugin('rightpress-live-product-update');

            // Inject styles
            RightPress_Help::inject_stylesheet('rp-wcdpd-promotion-volume-pricing-table-styles', RP_WCDPD_PLUGIN_URL . '/extensions/promotion-volume-pricing-table/assets/styles.css', RP_WCDPD_VERSION);

            // Enqueue or inject scripts
            RightPress_Help::enqueue_or_inject_script('rp-wcdpd-promotion-volume-pricing-table-scripts', (RP_WCDPD_PLUGIN_URL . '/extensions/promotion-volume-pricing-table/assets/scripts.js'), array('jquery'), RP_WCDPD_VERSION, false, array(
                'ajaxurl' => admin_url('admin-ajax.php?rightpress_ajax=1'),
            ));
        }
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

        if ($html = RP_WCDPD_Promotion_Volume_Pricing_Table::get_pricing_table_html($data, $product, $is_variable)) {
            echo $html;
        }
    }

    /**
     * Get volume pricing table html
     *
     * @access public
     * @param array $data
     * @param object $product
     * @param bool $is_variable
     * @return string
     */
    public static function get_pricing_table_html($data, $product, $is_variable)
    {

        // Allow developers to abort
        if (!apply_filters('rp_wcdpd_volume_pricing_table_display', true, $data, $product, $is_variable)) {
            return;
        }

        // Get table layout
        $layout = RP_WCDPD_Settings::get('promo_volume_pricing_table_layout');

        // Get table title
        $title = apply_filters('rp_wcdpd_volume_pricing_table_title', RP_WCDPD_Settings::get('promo_volume_pricing_table_title'), $product, $data, $is_variable);

        // Display pricing table for simple product
        if (!$is_variable) {

            // Include template and return html
            ob_start();
            RightPress_Help::include_extension_template('promotion-volume-pricing-table', $layout, RP_WCDPD_PLUGIN_PATH, RP_WCDPD_PLUGIN_KEY, array('title' => $title, 'data' => $data));
            return ob_get_clean();
        }
        // Display one table for all variations
        else if (RP_WCDPD_Settings::get('promo_volume_pricing_table_variation_layout') === 'single') {

            // Include template and return html
            ob_start();
            RightPress_Help::include_extension_template('promotion-volume-pricing-table', $layout, RP_WCDPD_PLUGIN_PATH, RP_WCDPD_PLUGIN_KEY, array('title' => $title, 'data' => $data));
            return ob_get_clean();
        }
        // Display individual table for product variation
        else {

            // Include template and return html
            ob_start();
            echo '<div id="rp_wcdpd_pricing_table_variation_' . $product->get_id() . '" class="rp_wcdpd_pricing_table_variation">'; // Note: This wrapper was left in order not to break custom styling based on ids/classes of this element
            RightPress_Help::include_extension_template('promotion-volume-pricing-table', $layout, RP_WCDPD_PLUGIN_PATH, RP_WCDPD_PLUGIN_KEY, array('title' => $title, 'data' => $data));
            echo '</div>';
            return ob_get_clean();
        }
    }

    /**
     * Display pricing table variation container
     *
     * @access public
     * @return void
     */
    public static function display_pricing_table_variation_container()
    {

        // Open variable product container
        echo '<div id="rp_wcdpd_pricing_table_variation_container" class="rp_wcdpd_pricing_table_variation_container" style="display: none;"></div>';
    }

    /**
     * Load variation pricing table via Ajax
     *
     * @access public
     * @return void
     */
    public function load_variation_pricing_table()
    {

        try {

            $html = null;

            // Get request data
            $request_data = RightPress_Product_Price::get_request_data_for_ajax_tools();

            // Load product object
            $object_id = !empty($request_data['variation_id']) ? $request_data['variation_id'] : $request_data['product_id'];
            $product = wc_get_product($object_id);

            // Unable to load product
            if (!$product) {
                throw new Exception('Unable to load product.');
            }

            // Unable to determine variation for variable product
            if ($product->is_type('variable')) {
                throw new Exception('Unable to determine product variation.');
            }

            // Product is not variation
            if (!$product->is_type('variation')) {
                throw new Exception('Product variation not provided.');
            }

            // Get rule for current variation
            if ($rule = self::get_applicable_volume_rule($product)) {

                // Get table data
                if ($table_data = self::get_table_data($product, $rule)) {

                    // Get table html
                    $html = RP_WCDPD_Promotion_Volume_Pricing_Table::get_pricing_table_html(array(array(
                        'product'       => $product,
                        'rule'          => $rule,
                        'table_data'    => $table_data,
                    )), $product, true);
                }
            }

            // Check if we have anything to display
            if ($html) {

                // Send success response
                echo json_encode(array(
                    'result'        => 'success',
                    'display'       => 1,
                    'html'          => $html,
                    'html_hash'     => md5($html),
                ));
            }
            // Nothing to display
            else {

                // Send success response
                echo json_encode(array(
                    'result'    => 'success',
                    'display'   => 0,
                ));
            }
        }
        catch (Exception $e) {

            // Send error response
            echo json_encode(array(
                'result'    => 'error',
                'message'   => $e->getMessage(),
            ));
        }

        exit;
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
        RightPress_Product_Price::start_running_custom_calculations();

        // Get product price
        $regular_price = $product->get_regular_price();
        $final_price = $product->get_price();

        // Unset flag
        RightPress_Product_Price::stop_running_custom_calculations();

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
