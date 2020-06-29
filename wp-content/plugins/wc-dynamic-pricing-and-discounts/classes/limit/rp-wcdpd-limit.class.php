<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Limit Controller
 *
 * @class RP_WCDPD_Limit
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
abstract class RP_WCDPD_Limit
{
    protected $total_limit          = null;
    protected $total_limit_snapshot = null;

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Filter adjustments by applying amount limits
     *
     * Note: Used for Cart Discounts and Checkout Fees only, Product Pricing
     * must be handled separately
     *
     * @access public
     * @param array $adjustments
     * @return array
     */
    public static function filter_adjustments($adjustments)
    {
        $filtered = array();

        // Get instance
        if ($class = get_called_class()) {
            if ($class !== 'RP_WCDPD_Limit') {
                if ($instance = $class::get_instance()) {

                    // Get method controller
                    if ($method_controller = $instance->get_method_controller()) {

                        // Iterate over adjustments
                        foreach ($adjustments as $rule_uid => $adjustment) {

                            // Get method
                            if ($rule_method = $method_controller->get_method_from_rule($adjustment['rule'])) {

                                // Get adjustment amount
                                $adjustment_amount = $rule_method->get_adjustment_amount($adjustment);

                                // Get limited amount
                                $limited_amount = $instance->get_method() ? $instance->limit_amount($adjustment_amount, $rule_method->get_cart_subtotal(true)) : $adjustment_amount;

                                // Do not add zero adjustments
                                if (RightPress_Product_Price::price_is_bigger_than($limited_amount, 0.0) || (RightPress_Product_Price::price_is_zero($limited_amount) && $instance->allow_zero_amount())) {

                                    // Round adjustment
                                    $limited_amount = $instance->round($limited_amount);

                                    // Add to filtered adjustments array
                                    $filtered[$rule_uid] = array_merge($adjustment, array('adjustment_amount' => $limited_amount));
                                }
                            }
                        }
                    }
                }
            }
        }

        return $filtered;
    }

    /**
     * Limit amount
     *
     * May return array with multiple quantity/amount pairs in case of quantitative handling
     *
     * @access public
     * @param float $amount
     * @param float $reference
     * @param string $cart_item_key
     * @param int $quantity_from
     * @param int $quantity_to
     * @param int $is_quantitative
     * @return float|array
     */
    protected function limit_amount($amount, $reference, $cart_item_key = null, $quantity_from = null, $quantity_to = null, $is_quantitative = false)
    {
        $return = $amount;
        $amount = (string) $amount;

        // Amount is not positive
        if ($amount <= 0) {
            return 0.0;
        }

        // Initialize limit
        if (!$this->init($reference, $cart_item_key, $quantity_from, $quantity_to)) {
            return 0.0;
        }

        // Get limit
        $limit = $this->get_limit($cart_item_key, $quantity_from, $quantity_to);

        // Limit is set
        if ($limit !== false) {

            $limit = (string) $limit;

            // Get total quantity and total amount
            $total_quantity = $is_quantitative ? ($quantity_to - $quantity_from + 1) : 1;
            $total_amount = $is_quantitative ? ($amount * $total_quantity) : $amount;

            // Limit covers amount in full
            if ($limit >= $total_amount) {
                $this->set_limit(($limit - $total_amount), $cart_item_key, $quantity_from, $quantity_to);
            }
            // Limit covers amount partially
            else if ($limit > 0) {

                // Quantitative amount handling
                if ($is_quantitative) {
                    $return = $this->apply_partial_quantitative_limit($amount, $limit, $total_quantity);
                }
                // Single amount handling
                else {
                    $return = (float) $limit;
                }

                // Set limit to zero
                $this->set_limit(0, $cart_item_key, $quantity_from, $quantity_to);
            }
            // Limit depleted
            else {
                $return = 0.0;
            }
        }

        return $return;
    }

    /**
     * Initialize limit
     *
     * @access protected
     * @param float $reference
     * @param string $cart_item_key
     * @param int $quantity_from
     * @param int $quantity_to
     * @return void
     */
    protected function init($reference = null, $cart_item_key = null, $quantity_from = null, $quantity_to = null)
    {
        // Already initialized
        if ($this->get_limit($cart_item_key, $quantity_from, $quantity_to) !== null) {
            return true;
        }

        // By default limit is not set
        $limit = false;

        // Limit is enabled
        if ($this->get_method()) {

            // Get limit value from settings and calculate initial limit
            if ($value = $this->get_value()) {
                $limit = $this->calculate_initial_limit($value, $reference, $cart_item_key, $quantity_from, $quantity_to);
            }

            // Limit is enabled but failed calculating it
            if ($limit === false) {
                return false;
            }
        }

        // Set limit
        $this->set_limit($limit, $cart_item_key, $quantity_from, $quantity_to);

        // Limit initialized
        return true;
    }

    /**
     * Calculate initial limit
     *
     * @access protected
     * @param float $value
     * @param float $reference
     * @param string $cart_item_key
     * @param int $quantity_from
     * @param int $quantity_to
     * @return float|bool
     */
    protected function calculate_initial_limit($value, $reference = null, $cart_item_key = null, $quantity_from = null, $quantity_to = null)
    {
        // Get initial limit value
        if ($initial_limit = $this->get_initial_limit_value($value, $reference)) {
            return $initial_limit;
        }

        // Unable to calculate initial limit
        return false;
    }

    /**
     * Get initial limit value
     *
     * @access protected
     * @param float $value
     * @param float $reference
     * @return float|bool
     */
    protected function get_initial_limit_value($value, $reference = null)
    {
        // Calculate percentage
        if (RightPress_Help::string_ends_with_substring($this->get_method(), '_percentage')) {

            // Reference amount is required for percentage limits
            if ($reference === null) {
                return false;
            }

            // Calculate percentage limit
            $value = ($reference * $value / 100);
        }

        // Return fixed value or value calculated based on percentage
        return $value;
    }

    /**
     * Get limit amount
     *
     * @access protected
     * @param string $cart_item_key
     * @param int $quantity_from
     * @param int $quantity_to
     * @return float|bool|null
     */
    protected function get_limit($cart_item_key = null, $quantity_from = null, $quantity_to = null)
    {
        return $this->total_limit;
    }

    /**
     * Set limit amount
     *
     * @access protected
     * @param flaot $limit
     * @param string $cart_item_key
     * @param int $quantity_from
     * @param int $quantity_to
     * @return float|bool|null
     */
    protected function set_limit($limit, $cart_item_key = null, $quantity_from = null, $quantity_to = null)
    {
        $this->total_limit = $limit;
    }

    /**
     * Reset limit
     *
     * @access public
     * @return void
     */
    public static function reset()
    {
        // Get instance
        if ($class = get_called_class()) {
            if ($class !== 'RP_WCDPD_Limit') {
                if ($instance = $class::get_instance()) {

                    // Reset limit
                    $instance->total_limit = null;
                }
            }
        }
    }

    /**
     * Round limited amount
     *
     * @access protected
     * @param float $amount
     * @return float
     */
    protected function round($amount)
    {
        return $amount;
    }

    /**
     * Get limit method from settings
     *
     * @access public
     * @return string|bool
     */
    public function get_method()
    {
        return RP_WCDPD_Settings::get($this->context . '_total_limit');
    }

    /**
     * Get limit value from settings
     *
     * @access public
     * @return float|bool
     */
    public function get_value()
    {

        // Get value
        $value = RP_WCDPD_Settings::get($this->context . '_total_limit_value');

        // Maybe convert value to currency
        if ($value && in_array($this->get_method(), array('price_discount_amount', 'total_discount_amount', 'total_amount'), true)) {

            $value = RightPress_Help::get_amount_in_currency($value);
        }

        // Return value
        return $value ? $value : false;
    }

    /**
     * Check whether or not zero amounts should be displayed
     *
     * @access public
     * @return bool
     */
    public function allow_zero_amount()
    {

        return false;
    }





}
