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
 * Product Pricing Method: Parent class for all methods that use quantity grouping
 *
 * @class RP_WCDPD_Method_Product_Pricing_Quantity
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
abstract class RP_WCDPD_Method_Product_Pricing_Quantity extends RP_WCDPD_Method_Product_Pricing
{

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        parent::__construct();
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

        // Get cart items with quantities to adjust
        $cart_items_to_adjust = $this->get_cart_items_to_adjust($rule, $cart_items);

        // Iterate over all cart items
        foreach ($cart_items as $cart_item_key => $cart_item) {

            // Check if rule applies to current cart item
            // Note: conditions are not checked here as they were checked when grouping quantities, if cart item is not there - conditions do not match
            if (isset($cart_items_to_adjust[$cart_item_key])) {

                // Get total receive quantity
                if ($receive_quantity = $this->get_total_receive_quantity($cart_items_to_adjust[$cart_item_key])) {

                    // Add adjustment to main array
                    $adjustments[$cart_item_key] = array(
                        'rule'                  => $rule,
                        'receive_quantity'      => $receive_quantity,
                        'quantity_breakdown'    => $cart_items_to_adjust[$cart_item_key],
                    );

                    // Get base price for reference amount calculation
                    $base_price = $this->get_base_price_for_reference_amount_calculation($cart_item_key, $cart_item);

                    // Calculate reference amount
                    $adjustments[$cart_item_key]['reference_amount'] = $this->get_reference_amount($adjustments[$cart_item_key], $base_price, $cart_item['quantity'], $cart_item['data'], $cart_item);
                }
            }
        }

        return $adjustments;
    }

    /**
     * Reserve quantities from quantity group
     *
     * @access public
     * @param array $quantity_group
     * @param array $used_quantities
     * @param int $reserve_quantity
     * @param bool $require_all
     * @param bool $cheapest_first
     * @return mixed
     */
    public function reserve_quantities($quantity_group, $used_quantities, $reserve_quantity, $require_all = false, $cheapest_first = false)
    {

        $quantities = array();

        // No items found
        if ($quantity_group === null) {
            return false;
        }

        // Maybe reverse quantity group element order if we need to reserve cheapest items first
        // Note: Quantity group is always sorted from the most expensive items to cheapest
        if ($cheapest_first) {
            $quantity_group = array_reverse($quantity_group, true);
        }

        // Iterate over cart item quantities in quantity group
        foreach ($quantity_group as $cart_item_key => $cart_item_quantity) {

            // Account for used quantities
            if (isset($used_quantities[$cart_item_key])) {
                $cart_item_quantity -= $used_quantities[$cart_item_key];
            }

            // All units used up
            if ($cart_item_quantity < 1) {
                continue;
            }

            // Proceed depending on which quantity is bigger
            if ($reserve_quantity > $cart_item_quantity) {
                $quantities[$cart_item_key] = $cart_item_quantity;
                $reserve_quantity -= $cart_item_quantity;
            }
            else {
                $quantities[$cart_item_key] = $reserve_quantity;
                $reserve_quantity = 0;
                break;
            }
        }

        // Return reserved quantities or false if min requirement was not met
        return (empty($quantities) || ($require_all && $reserve_quantity > 0)) ? false : $quantities;
    }

    /**
     * Merge cart item quantities
     *
     * @access public
     * @param array $output
     * @param array $input
     * @return array
     */
    public function merge_cart_item_quantities($output, $input)
    {
        foreach ($input as $cart_item_key => $quantity) {
            $output[$cart_item_key] = isset($output[$cart_item_key]) ? ($output[$cart_item_key] + $quantity) : $quantity;
        }

        return $output;
    }

    /**
     * Set purchase and receive quantities to cart items
     *
     * @access public
     * @param array $cart_items_to_adjust
     * @param int $quantities_to_purchase
     * @param int $quantities_to_receive
     * @param array $rule
     * @return array
     */
    public function set_purchase_and_receive_quantities_to_cart_items($cart_items_to_adjust, $quantities_to_purchase, $quantities_to_receive, $rule)
    {
        // Iterate over quantities to receive
        foreach ($quantities_to_receive as $cart_item_key => $quantity_to_receive) {

            // Check if current cart item is in purchase quantities
            if (!empty($quantities_to_purchase[$cart_item_key])) {

                // Add purchase quantity to skip
                $cart_items_to_adjust[$cart_item_key][] = array(
                    'purchase_quantity' => $quantities_to_purchase[$cart_item_key],
                );

                // Maybe add other purchase requirements
                end($cart_items_to_adjust[$cart_item_key]);
                $key = key($cart_items_to_adjust[$cart_item_key]);
                $cart_items_to_adjust[$cart_item_key][$key]['post_application_conditions'] = $this->get_post_application_conditions($quantities_to_purchase, $cart_item_key, $rule, $key, true);
            }

            // Add receive quantity
            $cart_items_to_adjust[$cart_item_key][] = array(
                'receive_quantity' => $quantity_to_receive,
            );

            // Maybe add purchase requirements
            end($cart_items_to_adjust[$cart_item_key]);
            $key = key($cart_items_to_adjust[$cart_item_key]);

            $cart_items_to_adjust[$cart_item_key][$key]['post_application_conditions'] = $this->get_post_application_conditions($quantities_to_purchase, $cart_item_key, $rule, $key, false);

        }

        return $cart_items_to_adjust;
    }

    /**
     * Get post application conditions from quantities to purchase (issue #320)
     *
     * @access protected
     * @param array $quantities_to_purchase
     * @param string $cart_item_key
     * @param array $rule
     * @param int $key
     * @param bool $is_purchase_quantity
     * @return array
     */
    protected function get_post_application_conditions($quantities_to_purchase, $cart_item_key, $rule, $key, $is_purchase_quantity)
    {
        // Maybe remove current cart item
        if ($is_purchase_quantity) {
            unset($quantities_to_purchase[$cart_item_key]);
        }

        // Check if any requirements are defined and format array
        return empty($quantities_to_purchase) ? array() : array(array(
            'method'        => 'paid_cart_items',
            'cart_items'    => $quantities_to_purchase,
            'action'        => 'skip_quantity_breakdown_iteration',
            'rule_uid'      => $rule['uid'],
            'data'          => array(
                'quantity_key'  => $key,
            ),
        ));
    }

    /**
     * Get total receive quantity for cart item
     *
     * @access public
     * @param array $cart_item_data
     * @return int
     */
    public function get_total_receive_quantity($cart_item_data)
    {
        $total = 0;

        foreach ($cart_item_data as $quantity_data) {
            if (!empty($quantity_data['receive_quantity'])) {
                $total += $quantity_data['receive_quantity'];
            }
        }

        return $total;
    }



}
