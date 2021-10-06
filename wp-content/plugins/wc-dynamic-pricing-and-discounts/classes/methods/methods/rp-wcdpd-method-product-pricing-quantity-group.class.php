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
 * Product Pricing Method: Group
 *
 * @class RP_WCDPD_Method_Product_Pricing_Quantity_Group
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
abstract class RP_WCDPD_Method_Product_Pricing_Quantity_Group extends RP_WCDPD_Method_Product_Pricing_Quantity
{

    protected $group_key        = 'group';
    protected $group_position   = 30;

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
        return esc_html__('Group', 'rp_wcdpd');
    }

    /**
     * Get cart items with quantities to adjust
     *
     * @access public
     * @param array $rule
     * @param array $cart_items
     * @return array
     */
    public function get_cart_items_to_adjust($rule, $cart_items = null)
    {
        $adjust = array();

        // Group cart item quantities
        $quantity_groups = $this->group_quantities($cart_items, $rule);

        // Sort quantity groups so that group products with fewer matched items
        // are processed first and therefore have higher chance of being filled
        // Related to issue #389
        RightPress_Help::stable_uasort($quantity_groups, array($this, 'group_product_quantity_group_compare'));

        // Make a copy
        $untouched = $quantity_groups;

        // Track used quantities that are not part of the adjusted quantities (issue #495)
        $used_non_adjusted_quantities = array();

        // Start infinite loop to take care of rule repetition, will break out of it by ourselves
        while (true) {

            // Store reserved quantities in a separate array temporary until we are sure that all group products have sufficient quantities
            $current = array();

            // Track cart item quantities that can no longer be considered
            $used_quantities = $this->merge_cart_item_quantities($adjust, $used_non_adjusted_quantities);

            // Iterate over group products
            foreach ($quantity_groups as $group_product_key => $group_product) {

                $product_found = false;

                // Make sure group product matched some items
                if ($group_product !== null) {

                    // Iterate over quantity groups for this group product
                    foreach ($group_product as $quantity_group_key => $quantity_group) {

                        // Reserve quantities for this quantity group
                        if ($reserved_quantities = $this->reserve_quantities($quantity_group, $used_quantities, $rule['group_products'][$group_product_key]['quantity'], true)) {

                            // Add to used quantities array
                            $used_quantities = $this->merge_cart_item_quantities($used_quantities, $reserved_quantities);

                            // If rule is not repeating, we must mark remaining quantity units as used so they are no longer
                            // available in case the !empty($untouched) part kicks in at the end of this method (issue #495)
                            if (!$this->repeat) {
                                foreach ($reserved_quantities as $cart_item_key => $quantity) {
                                    if ($quantity_group[$cart_item_key] > $quantity) {
                                        $used_non_adjusted_quantities[$cart_item_key] = $quantity_group[$cart_item_key] - $quantity;
                                    }
                                }
                            }

                            // Add to current array
                            $current = $this->merge_cart_item_quantities($current, $reserved_quantities);

                            // Remove items from untouched items array
                            foreach ($untouched as $untouched_group_product_key => $untouched_group_product) {

                                if (!is_array($untouched_group_product) || empty($untouched_group_product)) {
                                    unset($untouched[$untouched_group_product_key]);
                                    continue;
                                }

                                foreach ($untouched_group_product as $untouched_item_key => $untouched_item) {

                                    if (!is_array($untouched_item) || empty($untouched_item)) {
                                        unset($untouched[$untouched_group_product_key][$untouched_item_key]);
                                        continue;
                                    }

                                    foreach ($untouched_item as $cart_item_key => $quantity) {
                                        if (isset($reserved_quantities[$cart_item_key])) {
                                            unset($untouched[$untouched_group_product_key][$untouched_item_key][$cart_item_key]);
                                        }
                                    }

                                    if (isset($untouched[$untouched_group_product_key][$untouched_item_key]) && empty($untouched[$untouched_group_product_key][$untouched_item_key])) {
                                        unset($untouched[$untouched_group_product_key][$untouched_item_key]);
                                    }
                                }

                                if (empty($untouched[$untouched_group_product_key])) {
                                    unset($untouched[$untouched_group_product_key]);
                                }
                            }

                            // Mark product as found
                            $product_found = true;
                            break;
                        }
                    }
                }

                // At least one product was not found
                if (!$product_found) {

                    // Void current array
                    $current = array();

                    // Clear untouched items array
                    $untouched = array();

                    // Do not check other group products
                    break;
                }
            }

            // Check if full group of products was made up
            if (!empty($current)) {

                // Add to main array
                $adjust = $this->merge_cart_item_quantities($adjust, $current);

                // Rule repetition is enabled
                if ($this->repeat) {
                    continue;
                }
                // Note: this was commented out to solve #508, i.e. to make non-repeating rules truly non-repeating and not just for the same cart item
                // Note: uncommenting the block below will make a non-repeating group rule repeat on different cart items as described below
                // We still have untouched items (e.g. we need to repeat in case repetition is disabled, the group is "3 of any" and we have 3 x AAA and 3 x BBB in cart)
                // else if (!empty($untouched)) {
                //     continue;
                // }
            }

            // This loop can only be iterated explicitly, break out of it otherwise
            break;
        }

        // Format main array
        $adjust = $this->set_purchase_and_receive_quantities_to_cart_items(array(), array(), $adjust, $rule);

        return $adjust;
    }

    /**
     * Group quantities of matching cart items for Group rules
     *
     * @access public
     * @param array $cart_items
     * @param array $rule
     * @return array
     */
    public function group_quantities($cart_items, $rule)
    {
        $quantities = array();

        // Get Quantities Based On method
        $based_on = $rule['quantities_based_on'];

        // Filter out cart items that are not affected by this rule so we don't count them
        $cart_items = RP_WCDPD_Product_Pricing::filter_items_by_rules($cart_items, array($rule));

        // Iterate over group products
        foreach ($rule['group_products'] as $group_product_key => $group_product) {

            $match_found = false;

            // Iterate over cart items
            foreach ($cart_items as $cart_item_key => $cart_item) {

                // Get quantity
                $quantity = RightPress_Help::cart_item_is_bundle($cart_item) ? 0 : $cart_item['quantity'];

                // Get absolute product id (i.e. parent product id for variations)
                $product_id = RightPress_Help::get_wc_product_absolute_id($cart_item['data']);

                // Conditions are not matched, move to next cart item
                if (!RP_WCDPD_Controller_Conditions::conditions_are_matched(array($group_product), array('cart_item' => $cart_item, 'cart_items' => $cart_items))) {
                    continue;
                }

                // Match found
                $match_found = true;

                // Each individual product
                // Each individual variation (variation not specified)
                if ($based_on === 'individual__product' || ($based_on === 'individual__variation' && empty($cart_item['variation_id']))) {
                    $quantities[$group_product_key][$product_id][$cart_item_key] = $quantity;
                }
                // Each individual variation (variation specified)
                else if ($based_on === 'individual__variation') {
                    $quantities[$group_product_key][$cart_item['variation_id']][$cart_item_key] = $quantity;
                }
                //  Each individual cart line item
                else if ($based_on === 'individual__configuration') {
                    $quantities[$group_product_key][$cart_item_key][$cart_item_key] = $quantity;
                }
                // Each individual category
                else if ($based_on === 'cumulative__categories') {

                    // Get category ids
                    $categories = RightPress_Help::get_wc_product_category_ids_from_product_ids(array($product_id));

                    // No category is category by itself (issue #582)
                    if (empty($categories)) {
                        $categories[] = 0;
                    }

                    // Iterate over categories and add quantities
                    foreach ($categories as $category_id) {
                        $quantities[$group_product_key][$category_id][$cart_item_key] = $quantity;
                    }
                }
                // All quantities added up
                else if ($based_on === 'cumulative__all') {
                    $quantities[$group_product_key]['_all'][$cart_item_key] = $quantity;
                }
            }

            // Match not found
            if (!$match_found) {
                $quantities[$group_product_key] = null;
            }
        }

        // Return quantities
        return $quantities;
    }

    /**
     * Group product quantity group compare function
     *
     * @access public
     * @param array $a
     * @param array $b
     * @return int
     */
    public function group_product_quantity_group_compare($a, $b)
    {
        // Sort order doesn't matter if at least one element is null (group is not formed)
        if ($a === null || $b === null) {
            return 0;
        }

        $sum_a = 0;
        $sum_b = 0;

        foreach ($a as $group_key => $group) {
            $sum_a += array_sum($group);
        }

        foreach ($b as $group_key => $group) {
            $sum_b += array_sum($group);
        }

        if ($sum_a === $sum_b) {
            return 0;
        }
        else {
            return $sum_a > $sum_b ? 1 : -1;
        }
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
        // Special handling for per group pricing
        if (in_array($adjustment['rule']['group_pricing_method'], array('discount__amount_per_group', 'fixed__price_per_group'), true)) {
            return $this->apply_adjustment_to_prices_per_group_pricing($prices, $adjustment, $cart_item_key);
        }

        // Regular handling
        return parent::apply_adjustment_to_prices($prices, $adjustment, $cart_item_key);
    }

    /**
     * Apply adjustment to prices with per group pricing
     *
     * @access public
     * @param array $prices
     * @param array $adjustment
     * @param string $cart_item_key
     * @return array
     */
    public function apply_adjustment_to_prices_per_group_pricing($prices, $adjustment, $cart_item_key = null)
    {
        // Reference rule
        $rule = $adjustment['rule'];

        // Format call key - we want to have calls from get_reference_amount()
        // separate so we "reset" the counter when real requests start arrive
        $call_key = (isset($cart_item_key) ? '' : '_') . $rule['uid'];

        // Set up call counter on first call
        if (!isset(RP_WCDPD_Controller_Methods_Product_Pricing::$call_counter['group'][$call_key])) {
            RP_WCDPD_Controller_Methods_Product_Pricing::$call_counter['group'][$call_key] = 0;
        }

        // Get total quantity of all items in this group
        $all_quantities = wp_list_pluck($rule['group_products'], 'quantity');
        $total_quantity = (int) array_sum($all_quantities);

        // Get pricing value per quantity unit
        $pricing_value_per_unit = RightPress_Help::get_amount_in_currency($rule['group_pricing_value'], array('aelia', 'wpml')) / $total_quantity;

        // Prepare adjustment values
        $adjustment_value = round($pricing_value_per_unit, wc_get_price_decimals());
        $correcting_value = $this->get_correcting_adjustment_value($adjustment_value, $pricing_value_per_unit, $total_quantity);

        // Get receive quantity
        $receive_quantity = !empty($adjustment['receive_quantity']) ? (int) $adjustment['receive_quantity'] : 1;

        // Prepare adjustment quantities
        $adjustment_quantity = $receive_quantity;
        $correcting_quantity = 0;

        // Work out quantities with different adjustment amounts
        if (isset($correcting_value)) {

            // Track quantity left after each iteration
            $quantity_left = $receive_quantity;

            // Iterate over price ranges
            foreach ($prices['ranges'] as $price_range_index => $price_range) {

                // Get quantity to process
                $price_range_quantity = RightPress_Product_Price_Breakdown::get_price_range_quantity($price_range);
                $price_range_adjust_quantity = $quantity_left < $price_range_quantity ? $quantity_left : $price_range_quantity;
                $quantity_left -= $price_range_adjust_quantity;

                // Get existing group count
                $existing_group_count = floor(RP_WCDPD_Controller_Methods_Product_Pricing::$call_counter['group'][$call_key] / $total_quantity);

                // Get total units adjusted, including previous calls
                $total_adjusted = RP_WCDPD_Controller_Methods_Product_Pricing::$call_counter['group'][$call_key] + $price_range_adjust_quantity;

                // Get total group count including new groups
                $total_group_count = floor($total_adjusted / $total_quantity);

                // Get remainder
                $remainder = $total_adjusted - ($total_quantity * $total_group_count);

                // Make adjustments to quantities
                $current_group_count = $total_group_count - $existing_group_count;
                $adjustment_quantity -= $current_group_count;
                $correcting_quantity += $current_group_count;

                // Update call counter
                RP_WCDPD_Controller_Methods_Product_Pricing::$call_counter['group'][$call_key] = ($total_group_count * $total_quantity) + $remainder;

                // No more units to process
                if ($quantity_left <= 0) {
                    break;
                }
            }
        }

        // Track quantity left after each iteration
        $quantity_left = $receive_quantity;

        // Iterate over price ranges
        foreach ($prices['ranges'] as $price_range_index => $price_range) {

            // Get quantity to adjust for this price range
            $price_range_quantity = RightPress_Product_Price_Breakdown::get_price_range_quantity($price_range);
            $price_range_adjust_quantity = $quantity_left < $price_range_quantity ? $quantity_left : $price_range_quantity;
            $quantity_left -= $price_range_adjust_quantity;

            // Units adjusted by adjustment value
            if ($adjustment_quantity > 0) {

                // Get current quantity
                $current_quantity = $adjustment_quantity < $price_range_adjust_quantity ? $adjustment_quantity : $price_range_adjust_quantity;

                // Subtract current quantity from adjustment quantity as well as adjust quantity
                $adjustment_quantity -= $current_quantity;
                $price_range_adjust_quantity -= $current_quantity;

                // Get adjusted amount
                if ($rule['group_pricing_method'] === 'discount__amount_per_group') {
                    $adjusted_price = $price_range['price'] - $adjustment_value;
                    $adjusted_price = $adjusted_price >= 0 ? $adjusted_price : 0;
                }
                else {
                    $adjusted_price = $adjustment_value;
                }

                // Set adjusted price
                $this->prepare_and_set_adjusted_price($prices, $price_range_index, $current_quantity, $adjusted_price, $price_range['price'], $adjustment, $cart_item_key, array('receive_quantity' => $receive_quantity));
            }

            // Units adjusted by correcting adjustment value
            if ($price_range_adjust_quantity > 0 && $adjustment_quantity <= 0 && $correcting_quantity > 0) {

                // Get current quantity
                $current_quantity = $correcting_quantity < $price_range_adjust_quantity ? $correcting_quantity : $price_range_adjust_quantity;

                // Subtract current quantity from correcting quantity
                $correcting_quantity -= $current_quantity;

                // Get adjusted amount
                if ($rule['group_pricing_method'] === 'discount__amount_per_group') {
                    $adjusted_price = $price_range['price'] - $correcting_value;
                    $adjusted_price = $adjusted_price >= 0 ? $adjusted_price : 0;
                }
                else {
                    $adjusted_price = $correcting_value;
                }

                // Set adjusted price
                $this->prepare_and_set_adjusted_price($prices, $price_range_index, $current_quantity, $adjusted_price, $price_range['price'], $adjustment, $cart_item_key, array('receive_quantity' => $receive_quantity));
            }

            // No more units to adjust
            if ($quantity_left <= 0) {
                break;
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
        return RP_WCDPD_Pricing::adjust_amount($price_to_adjust, $rule['group_pricing_method'], $rule['group_pricing_value']);
    }


}
