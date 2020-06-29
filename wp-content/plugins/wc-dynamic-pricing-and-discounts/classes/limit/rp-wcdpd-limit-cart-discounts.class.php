<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load dependencies
if (!class_exists('RP_WCDPD_Limit')) {
    require_once('rp-wcdpd-limit.class.php');
}

/**
 * Cart Discount Limit Controller
 *
 * @class RP_WCDPD_Limit_Cart_Discounts
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
class RP_WCDPD_Limit_Cart_Discounts extends RP_WCDPD_Limit
{

    // Singleton control
    protected static $instance = false; public static function get_instance() { return self::$instance ? self::$instance : (self::$instance = new self()); }

    protected $context = 'cart_discounts';

    protected $limited_coupon_cart_item_amounts     = array();
    protected $coupon_cart_item_limit               = null;
    protected $processing_coupon_cart_item_limit    = false;
    protected $call_counter                         = array();

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
     * Get method controller
     *
     * @access protected
     * @return object
     */
    protected function get_method_controller()
    {

        return RP_WCDPD_Controller_Methods_Cart_Discount::get_instance();
    }

    /**
     * Get limited discount amount per coupon and cart item
     *
     * Note: Don't depend on $rule too much as it is set to the first adjustment
     * only in case of combined cart discounts
     *
     * @access public
     * @param float $amount
     * @param float $price_to_discount
     * @param string $coupon_code
     * @param array $cart_item_key
     * @param array $cart_item
     * @param array $rule
     * @return float
     */
    public static function get_limited_discount_amount_per_coupon_and_cart_item($amount, $price_to_discount, $coupon_code, $cart_item_key, $cart_item, $rule)
    {

        // Get instance
        $instance = RP_WCDPD_Limit_Cart_Discounts::get_instance();

        // Limits are not enabled
        if (!$instance->get_method()) {
            return $amount;
        }

        // Format coupon/item key
        $key = $coupon_code . '__' . $cart_item_key;

        // Increment call counter
        if (!isset($instance->call_counter[$key])) {
            $instance->call_counter[$key] = 0;
        }

        $instance->call_counter[$key]++;

        // Add call number to coupon/item key
        $key .= '_' . $instance->call_counter[$key];

        // Not yet limited, limit now
        if (!isset($instance->limited_coupon_cart_item_amounts[$key])) {

            // Add flag
            $instance->processing_coupon_cart_item_limit = true;

            // Get method controller
            $method_controller = $instance->get_method_controller();

            // Get method
            if ($rule_method = $method_controller->get_method_from_rule($rule)) {

                // Get limited amount
                $instance->limited_coupon_cart_item_amounts[$key] = $instance->round($instance->limit_amount($amount, $rule_method->get_cart_subtotal()));
            }
            else {

                // Unable to limit discount, applying zero discount to be safe
                $instance->limited_coupon_cart_item_amounts[$key] = 0.0;
            }

            // Remove flag
            $instance->processing_coupon_cart_item_limit = false;
        }

        // Return limited amount
        return $instance->limited_coupon_cart_item_amounts[$key];
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

        return $this->processing_coupon_cart_item_limit ? $this->coupon_cart_item_limit : $this->total_limit;
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

        if ($this->processing_coupon_cart_item_limit) {
            $this->coupon_cart_item_limit = $limit;
        }
        else {
            $this->total_limit = $limit;
        }
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
        $instance = RP_WCDPD_Limit_Cart_Discounts::get_instance();

        // Reset limit
        $instance->total_limit = null;

        // Reset coupon cart item limit
        $instance->coupon_cart_item_limit = null;

        // Reset cart item amounts array
        $instance->limited_coupon_cart_item_amounts = array();

        // Reset call counter
        $instance->call_counter = array();
    }

    /**
     * Check whether or not zero amounts should be displayed
     *
     * @access public
     * @return bool
     */
    public function allow_zero_amount()
    {

        return apply_filters('rp_wcdpd_allow_zero_cart_discount', false);
    }





}

RP_WCDPD_Limit_Cart_Discounts::get_instance();
