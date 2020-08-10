<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * RightPress Shared Product Price Breakdown
 *
 * @class RightPress_Product_Price_Breakdown
 * @package RightPress
 * @author RightPress
 */
final class RightPress_Product_Price_Breakdown
{

    /**
     * Generate prices array
     *
     * Pointers indicate the next price in row to be adjusted, pointers are plugin specific
     *
     * Prices array may be split into multiple ranges in the process. Due to the nature of the process,
     * there can be multiple ranges with no changes or multiple ranges with the same change.
     * That is, developers should not expect all quantity units with no changes to be in one range.
     *
     * Price ranges in $prices can only be split into more granular ranges but must never be merged back
     * until the process is complete
     *
     * WARNING! TODO! If changes are made to format of this array, they must also be made in RightPress_Product_Price_Test::merge_cart_items_price_changes()
     *
     * @access public
     * @param float $base_price
     * @param int $total_quantity
     * @param object $product
     * @return array
     */
    public static function generate_prices_array($base_price, $total_quantity, $product = null)
    {

        // Cast to float
        $base_price = (float) $base_price;

        // Get highest price candidates
        $highest_price_candidates = array($base_price);

        // Maybe add product prices to highest price candidates
        if ($product !== null) {
            $highest_price_candidates[] = (float) $product->get_price('edit');
            $highest_price_candidates[] = (float) $product->get_regular_price('edit');
        }

        // Format and return array
        return array(
            'pointers'  => apply_filters('rightpress_product_price_breakdown_prices_array_pointers', array()),
            'ranges'    => array(
            ('to_' . $total_quantity) => array(
                'from'          => 1,
                'to'            => $total_quantity,
                'price'         => $base_price,
                'base_price'    => $base_price,
                'highest_price' => max($highest_price_candidates),
                'new_changes'   => array(),         // Note: Each plugin must set its changes data under plugin key, key of each change must be unique if plugin needs to ensure it's not override by changes of another range or cart item when they are merged
                'all_changes'   => array(),         // Note: 'new_changes' and 'all_changes' separation is legacy handling that was important before WCDPD issue #639
            ),
        ));
    }

    /**
     * Calculate average price from prices array
     *
     * @access public
     * @param array $prices
     * @param float $default_price
     * @param object $product
     * @param array $cart_item
     * @param bool $ignore_empty_changes
     * @return float
     */
    public static function get_price_from_prices_array($prices, $default_price, $product, $cart_item = null, $ignore_empty_changes = false)
    {

        $subtotal = 0.0;
        $quantity = 0;

        // Iterate over price ranges
        foreach ($prices['ranges'] as $price_range_index => $price_range) {

            // Get price range quantity
            $price_range_quantity = RightPress_Product_Price_Breakdown::get_price_range_quantity($price_range);

            // Get current price
            $current_price = (!empty($price_range['all_changes']) || $ignore_empty_changes) ? $price_range['price'] : $default_price;

            // Calculate subtotal
            $subtotal += ($current_price * $price_range_quantity);

            // Increase quantity
            $quantity += $price_range_quantity;
        }

        // Sanity check
        if (!$quantity) {
            return $default_price;
        }

        // Calculate average price
        $average_price = $subtotal / $quantity;

        // Round cart item price so that we end up with correct cart line subtotal
        $rounded_average_price = RightPress_Product_Price::round($average_price);

        // Check for rounding errors and fix them by skipping default rounding
        if (RightPress_Product_Price::prices_differ($subtotal, ($rounded_average_price * $quantity))) {
            $rounded_average_price = RightPress_Product_Price::round($average_price, null, true);
        }

        // Allow developers to override
        $rounded_average_price = apply_filters('rightpress_product_price_breakdown_average_price', $rounded_average_price, $prices['ranges'], $product, $cart_item);

        // Return price
        return $rounded_average_price;
    }

    /**
     * Filter prices array for test quantity
     *
     * @access public
     * @param array $prices
     * @param int $test_quantity
     * @return array
     */
    public static function filter_prices_array_for_test_quantity($prices, $test_quantity)
    {

        $filtered = array();

        // Reset price ranges sort order
        RightPress_Product_Price_Breakdown::reset_price_ranges_sort_order($prices);

        // Reset pointers
        $pointers_keys = array_keys($prices['pointers']);
        $filtered['pointers'] = array_fill_keys($pointers_keys, 1);

        // Reverse price ranges sort order
        $prices['ranges'] = array_reverse($prices['ranges'], true);

        // Iterate over price ranges
        foreach ($prices['ranges'] as $price_range_index => $price_range) {

            // Get price range quantity
            $price_range_quantity = RightPress_Product_Price_Breakdown::get_price_range_quantity($price_range);

            // Fix quantity from if price range quantity is bigger than test quantity
            if ($price_range_quantity > $test_quantity) {
                $price_range['from'] = $price_range['from'] + ($price_range_quantity - $test_quantity);
            }

            // Add to filtered array
            $filtered['ranges'][$price_range_index] = $price_range;

            // Do not proceed to another price range if test quantity was met
            if ($price_range_quantity >= $test_quantity) {
                break;
            }
            // Otherwise decrement test quantity by quantity covered by current price range
            else {
                $test_quantity -= $price_range_quantity;
            }
        }

        // Reverse prices sort order again to match initial order
        $filtered['ranges'] = array_reverse($filtered['ranges'], true);

        // Return filtered prices array
        return $filtered;
    }

    /**
     * Get price range quantity from price range
     *
     * @access public
     * @param array $price_range
     * @return int
     */
    public static function get_price_range_quantity($price_range)
    {

        return ($price_range['to'] - $price_range['from'] + 1);
    }

    /**
     * Get total quantity from price ranges
     *
     * @access public
     * @param array $price_ranges
     * @return int
     */
    public static function get_price_ranges_total_quantity($price_ranges)
    {

        $total_quantity = 0;

        // Iterate over price ranges
        foreach ($price_ranges as $price_range) {
            $total_quantity += RightPress_Product_Price_Breakdown::get_price_range_quantity($price_range);
        }

        return $total_quantity;
    }

    /**
     * Sort price ranges in accordance with the current pointer position
     *
     * List of price ranges start from the range that the pointer points to
     * and all ranges prior to it are appended to the end of the array
     *
     * @access public
     * @param array $prices
     * @param string $pointer_key
     * @return void
     */
    public static function sort_price_ranges_to_pointer_position(&$prices, $pointer_key)
    {

        // Reset price ranges sort order
        RightPress_Product_Price_Breakdown::reset_price_ranges_sort_order($prices);

        // Get current range index
        $pointed_range_index = RightPress_Product_Price_Breakdown::get_pointed_price_range_index($prices, $pointer_key);

        // Sort price ranges so that they start from the one the pointer is pointing at
        $prices['ranges'] = RightPress_Help::sort_array_to_start_from_key($prices['ranges'], $pointed_range_index);
    }

    /**
     * Reset price ranges sort order
     *
     * @access public
     * @param array $prices
     * @return void
     */
    public static function reset_price_ranges_sort_order(&$prices)
    {

        ksort($prices['ranges']);
    }

    /**
     * Get index of the price range that the specified pointer points to
     *
     * @access public
     * @param array $prices
     * @param string $pointer_key
     * @return string
     */
    public static function get_pointed_price_range_index($prices, $pointer_key)
    {

        foreach ($prices['ranges'] as $price_range_index => $price_range) {
            if ($price_range['from'] <= $prices['pointers'][$pointer_key] && $prices['pointers'][$pointer_key] <= $price_range['to']) {
                return $price_range_index;
            }
        }

        // Return index of first price range
        reset($prices['ranges']);
        return key($prices['ranges']);
    }

    /**
     * Move specified price range pointer
     *
     * @access public
     * @param array $prices
     * @param string $pointer_key
     * @param int $quantity
     * @return void
     */
    public static function move_price_range_pointer(&$prices, $pointer_key, $quantity)
    {

        // Get price ranges total quantity
        $total_quantity = RightPress_Product_Price_Breakdown::get_price_ranges_total_quantity($prices['ranges']);

        // Increment pointer
        $incremented_pointer = $prices['pointers'][$pointer_key] + $quantity;

        // Set incremented pointer or calculate reset pointer position
        $prices['pointers'][$pointer_key] = ($incremented_pointer <= $total_quantity) ? $incremented_pointer : ($incremented_pointer - $total_quantity);
    }

    /**
     * Set price to prices array
     *
     * Note: This method must not reset existing indexes of $prices values, otherwise this would cause issues in other methods
     *
     * @access public
     * @param array $prices
     * @param string $pointer_key
     * @param float $price
     * @param int $price_range_index
     * @param int $quantity
     * @param array $changes
     * @param bool $skip_non_adjusted_quantity
     * @return void
     */
    public static function set_price_to_prices_array(&$prices, $pointer_key, $price, $price_range_index, $quantity, $changes = array(), $skip_non_adjusted_quantity = false)
    {

        // Check if prices differ
        $prices_differ = RightPress_Product_Price::prices_differ($prices['ranges'][$price_range_index]['price'], $price);

        // Note: We used to check if price that is being set differs from current price and would return if $skip_non_adjusted_quantity was not set to true, changed this to solve WCDPD isue #653

        // Reference price range
        $price_range = $prices['ranges'][$price_range_index];

        // Set adjusted price
        $price_range['price'] = $price;

        // Iterate over changes
        // Note: We add plugin changes even if prices do not differ because we need to know what rules were already processed (WCDPD issue #653)
        foreach ($changes as $plugin_key => $plugin_changes) {

            // Plugin changes array does not exists yet
            if (!isset($price_range['new_changes'][$plugin_key])) {
                $price_range['new_changes'][$plugin_key] = array();
            }

            // Set plugin changes
            $price_range['new_changes'][$plugin_key] = array_merge($price_range['new_changes'][$plugin_key], $plugin_changes);
        }

        // Quantities match
        if (RightPress_Product_Price_Breakdown::get_price_range_quantity($price_range) === $quantity) {

            // Set updated price data to the same range
            // Note: Before WCDPD issue #653 fix we used to check if prices differ here, now we always set updated data
            $prices['ranges'][$price_range_index] = $price_range;
        }
        // Quantities do not match
        else {

            // Set to quantity on the new range
            $price_range['to'] = ($prices['ranges'][$price_range_index]['from'] + $quantity - 1);

            // Increase from quantity of the current range to "make space" for the new range
            $prices['ranges'][$price_range_index]['from'] += $quantity;

            // Set updated price data to a new range inserted before current range
            $prices['ranges'] = RightPress_Help::insert_to_array_before_key($prices['ranges'], $price_range_index, array(('to_' . $price_range['to']) => $price_range));
        }

        // Move price range pointer if prices differ or if requested by plugins
        if ($prices_differ || $skip_non_adjusted_quantity) {

            RightPress_Product_Price_Breakdown::move_price_range_pointer($prices, $pointer_key, $quantity);
        }
    }

    /**
     * Get price breakdown for display
     *
     * @access public
     * @param array $price_data
     * @return array
     */
    public static function get_price_breakdown_for_display($price_data)
    {

        $breakdown = array();

        // Get display price decimals
        $decimals = RightPress_Product_Price::get_display_price_decimals();

        // Iterate over price ranges
        foreach ($price_data['prices']['ranges'] as $price_range) {

            // Get price
            $price = !empty($price_range['all_changes']) ? $price_range['price'] : $price_data['original_price'];

            // Get full price
            $full_price = RightPress_Product_Price_Changes::get_highest_price_from_cart_item_price_changes($price_data, $price_range);

            // Get price breakdown key
            $price_breakdown_key = RightPress_Product_Price_Breakdown::format_price_breakdown_key($price, $full_price, $decimals);

            // Get price range quantity
            $price_range_quantity = RightPress_Product_Price_Breakdown::get_price_range_quantity($price_range);

            // Add new price to main array
            if (!isset($breakdown[$price_breakdown_key])) {
                $breakdown[$price_breakdown_key] = array(
                    'price'         => $price,
                    'full_price'    => $full_price,
                    'quantity'      => 0,
                );
            }

            // Increment quantity of current price
            $breakdown[$price_breakdown_key]['quantity'] += $price_range_quantity;

            // Add change data
            foreach ($price_range['all_changes'] as $plugin_key => $plugin_changes) {

                // Plugin changes array does not exists yet
                if (!isset($breakdown[$price_breakdown_key]['all_changes'][$plugin_key])) {
                    $breakdown[$price_breakdown_key]['all_changes'][$plugin_key] = array();
                }

                // Set plugin changes
                $breakdown[$price_breakdown_key]['all_changes'][$plugin_key] = array_merge($breakdown[$price_breakdown_key]['all_changes'][$plugin_key], $plugin_changes);
            }
        }

        // Sort list of prices
        krsort($breakdown);

        // Return price breakdown
        return $breakdown;
    }

    /**
     * Format price breakdown key
     *
     * @access public
     * @param float $price
     * @param float $full_price
     * @param int $decimals
     * @return string
     */
    public static function format_price_breakdown_key($price, $full_price, $decimals)
    {

        return number_format($price, $decimals) . '-' . number_format($full_price, $decimals);
    }





}
