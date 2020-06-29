<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Methods related to WooCommerce Checkout
 *
 * @class RP_WCDPD_WC_Checkout
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
class RP_WCDPD_WC_Checkout
{

    // Singleton control
    protected static $instance = false; public static function get_instance() { return self::$instance ? self::$instance : (self::$instance = new self()); }

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        // Save cart discount and checkout fee data to order meta
        add_action('woocommerce_checkout_create_order', array($this, 'save_cart_data_to_order_meta'), 10, 2);

        // Save product pricing adjustment data to order item meta
        add_action('woocommerce_checkout_create_order_line_item', array($this, 'save_cart_item_data_to_order_item_meta'), 10, 4);
    }

    /**
     * Save cart discount and checkout fee data to order meta
     *
     * @access public
     * @param WC_Order $order
     * @param array $posted
     * @return void
     */
    public function save_cart_data_to_order_meta($order, $posted)
    {

        // Get applicable cart discount data
        if ($cart_discount_data = RP_WCDPD_Controller_Methods_Cart_Discount::get_instance()->applicable_adjustments) {

            // Add cart discount data to order meta
            $order->add_meta_data('_rp_wcdpd_cart_discount_data', $cart_discount_data);
        }

        // Get applicable checkout fee data
        if ($checkout_fee_data = RP_WCDPD_Controller_Methods_Checkout_Fee::get_instance()->applicable_adjustments) {

            // Add checkout fee data to order meta
            $order->add_meta_data('_rp_wcdpd_checkout_fee_data', $checkout_fee_data);
        }
    }

    /**
     * Save product pricing adjustment data to order item meta
     *
     * @access public
     * @param WC_Order_Item_Product $order_item
     * @param string $cart_item_key
     * @param array $values
     * @param WC_Order $order
     * @return void
     */
    public function save_cart_item_data_to_order_item_meta($order_item, $cart_item_key, $values, $order)
    {

        // Get price changes for current cart item
        if ($price_changes = RightPress_Product_Price_Cart::get_cart_item_price_changes($cart_item_key)) {

            // Check if there were any changes made by this plugin
            if (!empty($price_changes['all_changes']['rp_wcdpd'])) {

                // Add full product pricing adjustment data to order item meta
                $order_item->add_meta_data('_rp_wcdpd_product_pricing_data', $price_changes);
            }
        }
    }





}

RP_WCDPD_WC_Checkout::get_instance();
