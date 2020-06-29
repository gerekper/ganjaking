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
 * Product Pricing Limit Controller
 *
 * @class RP_WCDPD_Limit_Product_Pricing
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
class RP_WCDPD_Limit_Product_Pricing extends RP_WCDPD_Limit
{

    // Singleton control
    protected static $instance = false; public static function get_instance() { return self::$instance ? self::$instance : (self::$instance = new self()); }

    protected $context = 'product_pricing';

    protected $price_limit          = array();
    protected $price_limit_snapshot = array();

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
        return RP_WCDPD_Controller_Methods_Product_Pricing::get_instance();
    }

    /**
     * Limit product price discount
     *
     * Returns array of arrays with quantity/discount pairs
     *
     * @access public
     * @param float $discount
     * @param float $reference
     * @param string $cart_item_key
     * @param int $quantity_from
     * @param int $quantity_to
     * @return array
     */
    public static function limit_discount($discount, $reference, $cart_item_key, $quantity_from, $quantity_to)
    {
        // Get instance
        $instance = self::get_instance();

        // Limit discount
        $limited_discount = $instance->limit_amount($discount, $reference, $cart_item_key, $quantity_from, $quantity_to, !$instance->is_price_limit());

        // Float value represents full quantity range
        if (is_float($limited_discount)) {
            $limited_discount = array(array(
                'quantity'  => ($quantity_to - $quantity_from + 1),
                'discount'  => $limited_discount,
            ));
        }

        // Return limited discount
        return $limited_discount;
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
        // Cart item details are required but were not provided
        if ($this->is_price_limit() && ($cart_item_key === null || $quantity_from === null || $quantity_to === null)) {
            return false;
        }

        // Get initial limit value
        return parent::calculate_initial_limit($value, $reference, $cart_item_key, $quantity_from, $quantity_to);
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
        $limit = null;

        // Price limit
        if ($this->is_price_limit()) {

            // Check if required arguments are set
            if ($cart_item_key !== null && $quantity_from !== null && $quantity_to !== null) {

                // Check if any range limit is set for this cart item
                if (isset($this->price_limit[$cart_item_key])) {

                    // Iterate over range limits for this cart item
                    foreach ($this->price_limit[$cart_item_key] as $range_limit) {

                        // Check if requested range falls within current range
                        if ($range_limit['from'] <= $quantity_from && $quantity_to <= $range_limit['to']) {

                            // Limit found
                            $limit = $range_limit['limit'];
                            break;
                        }
                    }
                }
            }
        }
        // Total limit
        else {
            $limit = $this->total_limit;
        }

        // Return limit
        return $limit;
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
        // Price limit
        if ($this->is_price_limit()) {

            // Check if required arguments are set
            if ($cart_item_key !== null && $quantity_from !== null && $quantity_to !== null) {

                // Check if any range limit is set for this cart item
                if (isset($this->price_limit[$cart_item_key])) {

                    // Iterate over range limits for this cart item
                    foreach ($this->price_limit[$cart_item_key] as $range_limit_index => $range_limit) {

                        // Check if requested range falls within current range
                        if ($range_limit['from'] <= $quantity_from && $quantity_to <= $range_limit['to']) {

                            // Unset current range
                            unset($this->price_limit[$cart_item_key][$range_limit_index]);

                            // Original limit remaining on the from side
                            if ($range_limit['from'] < $quantity_from) {
                                $this->price_limit[$cart_item_key][] = array(
                                    'from'  => $range_limit['from'],
                                    'to'    => ($quantity_from - 1),
                                    'limit' => $range_limit['limit'],
                                );
                            }

                            // Original limit remaining on the to side
                            if ($quantity_to < $range_limit['to']) {
                                $this->price_limit[$cart_item_key][] = array(
                                    'from'  => ($quantity_to + 1),
                                    'to'    => $range_limit['to'],
                                    'limit' => $range_limit['limit'],
                                );
                            }

                            // Do not proceed
                            break;
                        }
                    }

                    // Add range limit
                    $this->price_limit[$cart_item_key][] = array(
                        'from'  => $quantity_from,
                        'to'    => $quantity_to,
                        'limit' => $limit,
                    );
                }
                // No range limit is set for this cart item
                else {

                    // Set first range limit
                    $this->price_limit[$cart_item_key] = array(
                        array(
                            'from'  => $quantity_from,
                            'to'    => $quantity_to,
                            'limit' => $limit,
                        ),
                    );
                }
            }
        }
        // Total limit
        else {
            $this->total_limit = $limit;
        }
    }

    /**
     * Check if limit is per product price
     *
     * @access protected
     * @return bool
     */
    protected function is_price_limit()
    {
        return in_array($this->get_method(), array('price_discount_amount', 'price_discount_percentage'), true);
    }

    /**
     * Store snapshot of remaining limits in memory
     *
     * @access public
     * @return void
     */
    public static function take_snapshot()
    {
        // Get instance
        $instance = RP_WCDPD_Limit_Product_Pricing::get_instance();

        // Take snapshots
        $instance->total_limit_snapshot = $instance->total_limit;
        $instance->price_limit_snapshot = $instance->price_limit;
    }

    /**
     * Reset limits to previously taken snapshot
     *
     * @access public
     * @return void
     */
    public static function reset_to_snapshot()
    {
        // Get instance
        $instance = RP_WCDPD_Limit_Product_Pricing::get_instance();

        // Reset limits
        $instance->total_limit = $instance->total_limit_snapshot;
        $instance->price_limit = $instance->price_limit_snapshot;
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
        $instance = RP_WCDPD_Limit_Product_Pricing::get_instance();

        // Reset limits
        $instance->total_limit = null;
        $instance->price_limit = array();
    }

    /**
     * Apply partial quantitative limit
     *
     * @access public
     * @param float $amount
     * @param float $limit
     * @param int $total_quantity
     * @return array
     */
    public function apply_partial_quantitative_limit($amount, $limit, $total_quantity)
    {
        $return = array();

        // Quantity of units fully covered
        if ($quantity_fully_covered = floor($limit / $amount)) {
            $return[] = array(
                'quantity'  => $quantity_fully_covered,
                'discount'  => (float) $amount,
            );
        }

        // Remainder for one more quantity unit
        if ($remainder = ($limit - ($quantity_fully_covered * $amount))) {
            $return[] = array(
                'quantity'  => 1,
                'discount'  => (float) $remainder,
            );
        }

        // Quantity with depleted limit
        if ($quantity_depleted = ($total_quantity - $quantity_fully_covered - ($remainder > 0 ? 1 : 0))) {
            $return[] = array(
                'quantity'  => $quantity_depleted,
                'discount'  => 0.0,
            );
        }

        return $return;
    }





}

RP_WCDPD_Limit_Product_Pricing::get_instance();
