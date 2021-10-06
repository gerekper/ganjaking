<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load dependencies
if (!class_exists('RP_WCDPD_Method_Product_Pricing')) {
    require_once('rp-wcdpd-method-product-pricing.class.php');
}

/**
 * Product Pricing Method: Volume
 *
 * @class RP_WCDPD_Method_Product_Pricing_Volume
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
abstract class RP_WCDPD_Method_Product_Pricing_Volume extends RP_WCDPD_Method_Product_Pricing
{

    protected $group_key        = 'volume';
    protected $group_position   = 20;

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
        return esc_html__('Volume', 'rp_wcdpd');
    }

    /**
     * Get cart item adjustments by rule
     *
     * @access public
     * @param array $rule
     * @param array $cart_items
     * @return array
     */
    public function get_adjustments($rule, $cart_items = null)
    {
        $adjustments = array();

        // No quantity ranges defined
        if (empty($rule['quantity_ranges'])) {
            return $adjustments;
        }

        // Add missing 1-X range to Tiered pricing rule so we correctly skip items that are not adjusted
        if ($rule['method'] === 'tiered') {
            $rule['quantity_ranges'] = RP_WCDPD_Pricing::add_volume_pricing_rule_missing_quantity_ranges($rule['quantity_ranges'], false);
        }

        // Get cart item quantities allocated to quantity ranges
        $allocated_quantities = $this->get_quantities_allocated_to_quantity_ranges($cart_items, $rule);

        // Iterate over cart items
        foreach ($cart_items as $cart_item_key => $cart_item) {

            // Check if rule applies to current cart item
            // Note: conditions are not checked here as they were checked when fetching applicable quantity ranges, if cart item is not there - conditions do not match
            if (isset($allocated_quantities[$cart_item_key])) {

                // Do not proceed if no quantity units were allocated for this cart item
                // Note: We bring cart items with zero allocated quantities here to solve issue #571
                if (!array_sum(wp_list_pluck($allocated_quantities[$cart_item_key], 'quantity'))) {
                    unset($allocated_quantities[$cart_item_key]);
                    continue;
                }

                // Add adjustment to main array
                $adjustments[$cart_item_key] = array(
                    'rule'              => $rule,
                    'quantity_ranges'   => $allocated_quantities[$cart_item_key],
                );
            }
        }

        // Get cumulative quantities per range
        $cumulative_quantities_per_range = $this->get_cumulative_quantities_per_range($adjustments);

        // Iterate over adjustments
        foreach ($adjustments as $cart_item_key => $adjustment) {

            // Set cumulative quantities per range to each item to get a potential fixed per-range pricing right (issue #519)
            $adjustments[$cart_item_key]['cumulative_quantities_per_range'] = $cumulative_quantities_per_range;

            // Reference cart item
            $cart_item = $cart_items[$cart_item_key];

            // Get base price for reference amount calculation
            $base_price = $this->get_base_price_for_reference_amount_calculation($cart_item_key, $cart_item);

            // Set reference amount
            $adjustments[$cart_item_key]['reference_amount'] = $this->get_reference_amount(array(
                'rule'                              => $rule,
                'quantity_ranges'                   => $allocated_quantities[$cart_item_key],
                'cumulative_quantities_per_range'   => $cumulative_quantities_per_range,
            ), $base_price, $cart_item['quantity'], $cart_item['data'], $cart_item);
        }

        return $adjustments;
    }

    /**
     * Get cart item quantities allocated to quantity ranges
     *
     * @access public
     * @param array $cart_items
     * @param array $rule
     * @return array
     */
    public function get_quantities_allocated_to_quantity_ranges($cart_items, $rule)
    {
        $ranges = array();

        // Prepare cart items for quantity range allocation
        $cart_items = $this->prepare_cart_items_for_quantity_range_allocation($cart_items);

        // Group quantities
        $quantity_groups = $this->group_quantities($cart_items, $rule);

        // Iterate over quantity groups
        foreach ($quantity_groups as $quantity_group_key => $quantity_group) {

            // Get matching quantity range keys with allocated cart item quantities
            $quantity_range_keys_with_quantities = $this->get_quantity_ranges_with_allocated_quantities($rule, $quantity_group, $quantity_group_key);

            // Iterate over quantity range keys with quantities
            foreach ($quantity_range_keys_with_quantities as $unique_range_key => $data) {

                // Iterate over cart items with quantities
                foreach ($data['quantities'] as $cart_item_key => $quantity) {
                    $ranges[$cart_item_key][$unique_range_key] = array(
                        'quantity_range_key'    => $data['quantity_range_key'],
                        'quantity_group_key'    => $data['quantity_group_key'],
                        'quantity'              => $quantity,
                    );
                }
            }
        }

        return $ranges;
    }

    /**
     * Get cumulative quantities per range
     *
     * @access public
     * @param array $adjustments
     * @return array
     */
    public function get_cumulative_quantities_per_range($adjustments)
    {
        // Count cumulative quantities per range
        $cumulative_quantities_per_range = array();

        // Iterate over adjustments
        foreach ($adjustments as $cart_item_key => $adjustment) {
            foreach ($adjustment['quantity_ranges'] as $unique_range_key => $data) {

                // Add range
                if (!isset($cumulative_quantities_per_range[$unique_range_key])) {
                    $cumulative_quantities_per_range[$unique_range_key] = $data;
                }
                // Increment range quantity
                else {
                    $cumulative_quantities_per_range[$unique_range_key]['quantity'] += $data['quantity'];
                }
            }
        }

        return $cumulative_quantities_per_range;
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

        // Format call key - we want to have calls from get_reference_amount()
        // separate so we "reset" the counter when real requests start arrive
        $call_key = (isset($cart_item_key) ? '' : '_') . $rule['uid'];

        // Set up call counter on first call
        if (!isset(RP_WCDPD_Controller_Methods_Product_Pricing::$call_counter['volume'][$call_key])) {
            RP_WCDPD_Controller_Methods_Product_Pricing::$call_counter['volume'][$call_key] = array();
        }

        // Check if non adjusted quantities should be skipped in $prices (issue #539)
        $skip_non_adjusted_quantity = ($rule['method'] === 'tiered');

        // Get quantity ranges
        $quantity_ranges = $adjustment['quantity_ranges'];

        // Iterate over price ranges
        foreach ($prices['ranges'] as $price_range_index => $price_range) {

            // Get price range quantity
            $price_range_quantity = RightPress_Product_Price_Breakdown::get_price_range_quantity($price_range);

            // Iterate over quantity ranges
            foreach ($quantity_ranges as $unique_range_key => $data) {

                $break = false;

                // Set up call counter on first call
                if (!isset(RP_WCDPD_Controller_Methods_Product_Pricing::$call_counter['volume'][$call_key][$unique_range_key])) {
                    RP_WCDPD_Controller_Methods_Product_Pricing::$call_counter['volume'][$call_key][$unique_range_key] = 0;
                }

                // Reference quantity range
                $quantity_range = $rule['quantity_ranges'][$data['quantity_range_key']];

                // Price range has excessive quantity
                if ($data['quantity'] < $price_range_quantity) {
                    $adjustment_quantity = $data['quantity'];
                    $price_range_quantity -= $adjustment_quantity;
                    unset($quantity_ranges[$unique_range_key]);
                }
                // Quantity range has excessive quantity
                else if ($data['quantity'] > $price_range_quantity) {
                    $adjustment_quantity = $price_range_quantity;
                    $quantity_ranges[$unique_range_key]['quantity'] = $data['quantity'] - $price_range_quantity;
                    $break = true;
                }
                // Quantities are equal
                else {
                    $adjustment_quantity = $price_range_quantity;
                    unset($quantity_ranges[$unique_range_key]);
                    $break = true;
                }

                // Increment call counter
                RP_WCDPD_Controller_Methods_Product_Pricing::$call_counter['volume'][$call_key][$unique_range_key] += $adjustment_quantity;

                // Special handling - pricing is set per range
                if ($quantity_range['pricing_method'] === 'fixed__price_per_range') {

                    $correcting_quantity = 0;

                    // Get pricing value
                    $pricing_value = RightPress_Help::get_amount_in_currency($quantity_range['pricing_value'], array('aelia', 'wpml'));

                    // Get cumulative quantity per range
                    $cumulative_quantity = $adjustment['cumulative_quantities_per_range'][$unique_range_key]['quantity'];

                    // Convert price per range to price per quantity unit
                    // Note: we use cumulative quantities of each range as multiple cart items may fall into the same range (issue #519)
                    $pricing_value_per_unit = $pricing_value / $cumulative_quantity;

                    // Get adjusted price
                    $adjusted_price = round($pricing_value_per_unit, wc_get_price_decimals());

                    // Check if this is the last call
                    if (RP_WCDPD_Controller_Methods_Product_Pricing::$call_counter['volume'][$call_key][$unique_range_key] >= $cumulative_quantity) {

                        // Check if last unit price has to be corrected (issue #515)
                        if ($correcting_price = $this->get_correcting_adjustment_value($adjusted_price, $pricing_value_per_unit, $cumulative_quantity)) {
                            $adjustment_quantity--;
                            $correcting_quantity++;
                        }
                    }

                    // Set adjusted price
                    if ($adjustment_quantity > 0) {
                        $this->prepare_and_set_adjusted_price($prices, $price_range_index, $adjustment_quantity, $adjusted_price, $price_range['price'], $adjustment, $cart_item_key, array('quantity_range' => $quantity_range), $skip_non_adjusted_quantity);
                    }

                    // Set correcting price
                    if ($correcting_quantity > 0) {
                        $this->prepare_and_set_adjusted_price($prices, $price_range_index, $correcting_quantity, $correcting_price, $price_range['price'], $adjustment, $cart_item_key, array('quantity_range' => $quantity_range), $skip_non_adjusted_quantity);
                    }
                }
                // Regular handling - pricing is set per quantity unit
                else {

                    // Get adjusted amount
                    $adjusted_price = RP_WCDPD_Pricing::adjust_amount($price_range['price'], $quantity_range['pricing_method'], $quantity_range['pricing_value']);

                    // Set adjusted price
                    $this->prepare_and_set_adjusted_price($prices, $price_range_index, $adjustment_quantity, $adjusted_price, $price_range['price'], $adjustment, $cart_item_key, array('quantity_range' => $quantity_range), $skip_non_adjusted_quantity);
                }

                // Break from quantity ranges loop if we need to go to the next price range
                if ($break) {
                    break;
                }
            }

            // No more quantity ranges left
            if (empty($quantity_ranges)) {
                break;
            }
        }

        // Return adjusted prices
        return $prices;
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
        return $cart_items;
    }




}
