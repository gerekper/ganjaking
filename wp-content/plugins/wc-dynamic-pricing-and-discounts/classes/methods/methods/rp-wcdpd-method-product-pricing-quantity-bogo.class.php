<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load dependencies
if (!class_exists('RP_WCDPD_Method_Product_Pricing_Quantity')) {
    require_once('rp-wcdpd-method-product-pricing-quantity.class.php');
}

/**
 * Product Pricing Method: BOGO
 *
 * @class RP_WCDPD_Method_Product_Pricing_Quantity_BOGO
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
abstract class RP_WCDPD_Method_Product_Pricing_Quantity_BOGO extends RP_WCDPD_Method_Product_Pricing_Quantity
{

    protected $group_key        = 'bogo';
    protected $group_position   = 40;

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        parent::__construct();

        $this->hook_group();
    }

    /**
     * Get group label
     *
     * @access public
     * @return string
     */
    public function get_group_label()
    {
        return esc_html__('Buy / Get', 'rp_wcdpd');
    }

    /**
     * Apply adjustment to prices
     *
     * @access public
     * @param array $prices
     * @param array $adjustment
     * @param string $cart_item_key
     * @return array
     */
    public function apply_adjustment_to_prices($prices, $adjustment, $cart_item_key = null)
    {
        // Reference rule
        $rule = $adjustment['rule'];

        // Get total receive quantity
        $total_receive_quantity = $this->get_total_receive_quantity($adjustment['quantity_breakdown']);

        // Iterate over quantity breakdown
        foreach ($adjustment['quantity_breakdown'] as $quantity_data) {

            // Maybe skip this iteration due to failed post application conditions (issue #320)
            if (!empty($adjustment['failed_post_application_conditions'])) {
                foreach ($adjustment['failed_post_application_conditions'] as $failed_condition) {
                    if (isset($failed_condition['action']) && $failed_condition['action'] === 'skip_quantity_breakdown_iteration') {
                        foreach ($quantity_data['post_application_conditions'] as $condition) {
                            if ($condition['data']['quantity_key'] === $failed_condition['data']['quantity_key']) {
                                continue 3;
                            }
                        }
                    }
                }
            }

            // Check if current quantity is purchase quantity
            $is_purchase = !empty($quantity_data['purchase_quantity']);

            // Get current quantity
            $current_quantity = $is_purchase ? $quantity_data['purchase_quantity'] : $quantity_data['receive_quantity'];

            // Track quantity left after each iteration
            $quantity_left = $current_quantity;

            // Iterate over price ranges
            foreach ($prices['ranges'] as $price_range_index => $price_range) {

                // Get quantity to adjust
                $price_range_quantity = RightPress_Product_Price_Breakdown::get_price_range_quantity($price_range);
                $price_range_adjust_quantity = $quantity_left < $price_range_quantity ? $quantity_left : $price_range_quantity;
                $quantity_left -= $price_range_adjust_quantity;

                // Get price adjusted by rule pricing method
                $adjusted_price = $is_purchase ? $price_range['price'] : $this->adjust_price_by_rule_pricing_method($price_range['price'], $rule);

                // Set adjusted price
                $this->prepare_and_set_adjusted_price($prices, $price_range_index, $price_range_adjust_quantity, $adjusted_price, $price_range['price'], $adjustment, $cart_item_key, array('receive_quantity' => $total_receive_quantity), $is_purchase);

                // Sort price ranges to pointer position
                RightPress_Product_Price_Breakdown::sort_price_ranges_to_pointer_position($prices, 'rp_wcdpd');

                // No more units to adjust
                if ($quantity_left <= 0) {
                    break;
                }
            }

            // Maybe set post application conditions to prices array (issue #320)
            if (!empty($quantity_data['post_application_conditions'])) {
                foreach ($quantity_data['post_application_conditions'] as $condition) {
                    $prices['post_application_conditions'][] = $condition;
                }
            }
        }

        // Return adjusted prices
        return $prices;
    }

    /**
     * Get price adjusted by rule pricing method
     *
     * @access public
     * @param float $price_to_adjust
     * @param array $rule
     * @return float
     */
    public function adjust_price_by_rule_pricing_method($price_to_adjust, $rule)
    {
        return RP_WCDPD_Pricing::adjust_amount($price_to_adjust, $rule['bogo_pricing_method'], $rule['bogo_pricing_value']);
    }


}
