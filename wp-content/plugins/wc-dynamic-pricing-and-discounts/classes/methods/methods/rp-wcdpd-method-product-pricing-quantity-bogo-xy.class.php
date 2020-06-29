<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load dependencies
if (!class_exists('RP_WCDPD_Method_Product_Pricing_Quantity_BOGO')) {
    require_once('rp-wcdpd-method-product-pricing-quantity-bogo.class.php');
}

/**
 * Product Pricing Method: BOGO XY
 *
 * @class RP_WCDPD_Method_Product_Pricing_Quantity_BOGO_XY
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
class RP_WCDPD_Method_Product_Pricing_Quantity_BOGO_XY extends RP_WCDPD_Method_Product_Pricing_Quantity_BOGO
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

        // Sort cart items by price descending so that we use more expensive items to trigger a rule and leave the cheaper ones to adjust
        $cart_items_desc = RightPress_Product_Price_Changes::sort_cart_items_by_price($cart_items, 'descending', true);

        // Group cart item quantities
        $quantity_groups = $this->group_quantities($cart_items_desc, $rule);

        // Prepare target quantity group
        $receive_quantity_group = $this->get_target_quantity_group($rule, $cart_items_desc);

        // Track cart item quantities that can no longer be considered (i.e. were either used to trigger rule or adjustment was applied to them)
        $used_quantities = array();

        // Iterate over quantity groups
        foreach ($quantity_groups as $quantity_group_key => $quantity_group) {

            $proceed_to_next_quantity_group = true;

            // Move quantity group items that are present in the receive quantity group to the end of the array (issue #504)
            foreach ($quantity_group as $cart_item_key => $quantity) {
                if (isset($receive_quantity_group[$cart_item_key])) {
                    unset($quantity_group[$cart_item_key]);
                    $quantity_group[$cart_item_key] = $quantity;
                }
            }

            // Start infinite loop to take care of repetition, will break out of it by ourselves
            while (true) {

                // Get quantities to purchase
                if ($quantities_to_purchase = $this->reserve_quantities($quantity_group, $used_quantities, $rule['bogo_purchase_quantity'], true)) {

                    // Mark quantities used temporary until we check if there are any items to be adjusted based this
                    $temporary_used_quantities = $this->merge_cart_item_quantities($used_quantities, $quantities_to_purchase);

                    // Get quantities to receive at adjusted price
                    if ($quantities_to_receive = $this->reserve_quantities($receive_quantity_group, $temporary_used_quantities, $rule['bogo_receive_quantity'], false, true)) {

                        // Mark quantities used
                        $used_quantities = $this->merge_cart_item_quantities($temporary_used_quantities, $quantities_to_receive);

                        // Set purchase and receive quantities to cart items
                        $adjust = $this->set_purchase_and_receive_quantities_to_cart_items($adjust, $quantities_to_purchase, $quantities_to_receive, $rule);

                        // Repeat this again for the same quantity group, we may have more quantity units to work with
                        if ($this->repeat) {
                            continue;
                        }
                        // Do not proceed to next quantity group if repetition is disabled since rule was applied to current quantity group
                        else {
                            $proceed_to_next_quantity_group = false;
                        }
                    }
                }

                // This loop can only be iterated explicitly, break out of it otherwise
                break;
            }

            // Do not proceed to next quantity group
            if (!$proceed_to_next_quantity_group) {
                break;
            }
        }

        return $adjust;
    }

    /**
     * Get other cart items to adjust
     *
     * @access public
     * @param array $rule
     * @param array $cart_items
     * @return array
     */
    public function get_target_quantity_group($rule, $cart_items)
    {
        $matched = array();

        // Get conditions
        $conditions = !empty($rule['bogo_product_conditions']) ? $rule['bogo_product_conditions'] : array();

        // Check each cart item
        foreach ($cart_items as $cart_item_key => $cart_item) {

            // Check condition against current cart item
            if (RP_WCDPD_Controller_Conditions::conditions_are_matched($conditions, array('cart_item' => $cart_item, 'cart_items' => $cart_items))) {
                $matched[$cart_item_key] = $cart_item['quantity'];
            }
        }

        return $matched;
    }


}
