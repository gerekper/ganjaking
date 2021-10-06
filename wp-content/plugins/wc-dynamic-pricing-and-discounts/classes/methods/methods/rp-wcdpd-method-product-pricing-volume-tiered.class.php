<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load dependencies
if (!class_exists('RP_WCDPD_Method_Product_Pricing_Volume')) {
    require_once('rp-wcdpd-method-product-pricing-volume.class.php');
}

/**
 * Product Pricing Method: Volume Tiered
 *
 * @class RP_WCDPD_Method_Product_Pricing_Volume_Tiered
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
class RP_WCDPD_Method_Product_Pricing_Volume_Tiered extends RP_WCDPD_Method_Product_Pricing_Volume
{

    protected $key      = 'tiered';
    protected $position = 20;

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

        $this->hook();
    }

    /**
     * Get label
     *
     * @access public
     * @return string
     */
    public function get_label()
    {
        return esc_html__('Tiered pricing', 'rp_wcdpd');
    }

    /**
     * Get matching quantity range keys with allocated cart item quantities
     *
     * @access public
     * @param array $rule
     * @param int $quantity_group
     * @param string $quantity_group_key
     * @return array
     */
    public function get_quantity_ranges_with_allocated_quantities($rule, $quantity_group, $quantity_group_key)
    {
        $matched = array();

        // Get total quantity
        $total_quantity = array_sum($quantity_group);

        // Iterate over quantity ranges
        foreach ($rule['quantity_ranges'] as $quantity_range_key => $quantity_range) {

            // Format unique range key
            $unique_range_key = $quantity_range_key . '_' . $quantity_group_key;

            // Add identifiers and a list of cart items with zero quantities to main array (quantities are updated later in the process)
            // Note: We add zero quantities to solve issue #571
            $matched[$unique_range_key] = array(
                'quantity_range_key'    => $quantity_range_key,
                'quantity_group_key'    => $quantity_group_key,
                'quantities'            => array_fill_keys(array_keys($quantity_group), 0),
            );

            // Include range if quantity falls into it
            if ($quantity_range['from'] === null || $quantity_range['from'] <= $total_quantity) {

                // Find out overlapping ranges
                $count_from_incl    = ($quantity_range['from'] === null ? 1 : $quantity_range['from']);
                $count_to_incl      = (($quantity_range['to'] === null || $total_quantity <= $quantity_range['to']) ? $total_quantity : $quantity_range['to']);

                // Track progress
                $working_quantity = 0;

                // Iterate over cart items in a quantity group
                foreach ($quantity_group as $cart_item_key => $cart_item_quantity) {

                    // Find out overlapping ranges
                    $current_count_from_incl    = $working_quantity + 1;
                    $current_count_to_incl      = $working_quantity + $cart_item_quantity;

                    // Update working quantity
                    $working_quantity = $current_count_to_incl;

                    // Check if this cart item falls into current range
                    if ($working_quantity >= $count_from_incl) {

                        // Update current overlapping ranges
                        $current_count_from_incl    = ($current_count_from_incl > $count_from_incl ? $current_count_from_incl : $count_from_incl);
                        $current_count_to_incl      = ($current_count_to_incl < $count_to_incl ? $current_count_to_incl : $count_to_incl);

                        // Add cart item to matched array
                        $matched[$unique_range_key]['quantities'][$cart_item_key] = ($current_count_to_incl - $current_count_from_incl + 1);

                        // Nothing else fits into this range
                        if ($working_quantity >= $count_to_incl) {
                            break;
                        }
                    }
                }
            }

            // Stop iterating if this was the last range that quantity falls into
            if ($quantity_range['to'] === null || $quantity_range['to'] >= $total_quantity) {
                break;
            }
        }

        // Check for zero quantities
        foreach ($matched as $unique_range_key => $data) {

            foreach ($data['quantities'] as $cart_item_key => $quantity) {
                if ($quantity < 1) {
                    unset($matched[$unique_range_key]['quantities'][$cart_item_key]);
                }
            }

            if (empty($matched[$unique_range_key]['quantities'])) {
                unset($matched[$unique_range_key]);
            }
        }

        return $matched;
    }

    /**
     * Prepare cart items for quantity range allocation
     *
     * @access public
     * @param array $cart_items
     * @return array
     */
    public function prepare_cart_items_for_quantity_range_allocation($cart_items)
    {
        // Sort cart items by price descending so that higher prices get lower discounts (issue #473)
        return RightPress_Product_Price_Changes::sort_cart_items_by_price($cart_items, 'descending', true);
    }



}

RP_WCDPD_Method_Product_Pricing_Volume_Tiered::get_instance();
