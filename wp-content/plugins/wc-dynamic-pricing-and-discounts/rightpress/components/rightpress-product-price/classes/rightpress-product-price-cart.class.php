<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * RightPress Shared Product Price Cart
 *
 * @class RightPress_Product_Price_Cart
 * @package RightPress
 * @author RightPress
 */
final class RightPress_Product_Price_Cart
{

    // Flags
    private $cart_loaded_from_session               = false;
    private $refreshing_prepared_cart_item_prices   = false;

    // Store cart item price change data in memory
    private $cart_item_price_changes                = array();
    private $cart_item_price_changes_environment    = array();

    // Track which "price set" actions have been triggered
    private $price_set_actions_triggered = array();

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

        // Check if functionality of this class is used by any plugin
        if (!RightPress_Product_Price_Cart::is_used()) {
            return;
        }

        // Cart loaded from session
        RightPress_Help::add_late_action('woocommerce_cart_loaded_from_session', array($this, 'cart_loaded_from_session'));
    }

    /**
     * Check if functionality of this class is used by any plugin
     *
     * @access public
     * @return bool
     */
    public static function is_used()
    {

        return has_filter('rightpress_product_price_cart_item_price_changes_first_stage_callbacks') || has_filter('rightpress_product_price_cart_item_price_changes_second_stage_callbacks');
    }

    /**
     * Cart loaded from session
     *
     * @access public
     * @param object $cart
     * @return void
     */
    public function cart_loaded_from_session($cart)
    {

        // Set flag
        $this->cart_loaded_from_session = true;

        // Maybe prepare cart item prices
        $this->maybe_prepare_cart_item_prices($cart);
    }

    /**
     * Get environment variables for prepared cart item prices invalidation
     *
     * @access public
     * @param object $cart
     * @return array
     */
    public function get_environment_variables($cart)
    {

        $variables = array();

        // Applied coupons
        $variables['applied_coupons'] = $cart->applied_coupons;

        // Iterate over cart items
        foreach ($cart->cart_contents as $cart_item_key => $cart_item) {

            // Cart item key
            $variables['cart_item_keys'][] = $cart_item_key;

            // Cart item quantity
            $variables['cart_item_quantities'][] = $cart_item['quantity'];

            // Cart item price as set on product
            $variables['cart_item_prices'][] = $cart_item['data']->get_price('edit');
        }

        return $variables;
    }

    /**
     * Maybe prepare cart item prices
     *
     * @access private
     * @param object $cart
     * @return void
     */
    private function maybe_prepare_cart_item_prices($cart)
    {

        // Check if cart has been loaded
        if (!$this->cart_loaded_from_session) {
            return;
        }

        // Cart is empty, nothing to do
        if (!is_array($cart->cart_contents) || empty($cart->cart_contents)) {
            return;
        }

        // Get price changes for cart items
        $price_changes = RightPress_Product_Price_Changes::get_price_changes_for_cart_items($cart->cart_contents);

        // Store price changes in memory
        $this->cart_item_price_changes = $price_changes;

        // Set environment variables
        $this->cart_item_price_changes_environment = $this->get_environment_variables($cart);

        // Prepared price changes
        if (!empty($price_changes)) {

            // Iterate over price changes
            foreach ($price_changes as $cart_item_key => $cart_item_price_change) {

                // Get cart item price change hash
                $hash = RightPress_Help::get_hash(false, array($cart_item_key, $cart_item_price_change));

                // Check if action should be triggered
                if (!in_array($hash, $this->price_set_actions_triggered, true)) {

                    // Trigger action
                    do_action('rightpress_product_price_cart_price_set', $cart_item_price_change['price'], $cart_item_key, $cart, $cart_item_price_change);

                    // Do not repeat the same action again
                    $this->price_set_actions_triggered[] = $hash;
                }
            }
        }
        // Prepared no price changes
        else {

            // Check if action should be triggered
            if (!in_array('no_changes', $this->price_set_actions_triggered, true)) {

                // Trigger action
                do_action('rightpress_product_price_cart_no_changes_to_prices', $cart);

                // Do not repeat the same action again
                $this->price_set_actions_triggered[] = 'no_changes';
            }
        }
    }

    /**
     * Get cart item price changes
     *
     * @access public
     * @param string $cart_item_key
     * @return array
     */
    public static function get_cart_item_price_changes($cart_item_key = null)
    {

        // Get instance
        $instance = RightPress_Product_Price_Cart::get_instance();

        // Return changes for single cart item
        if (isset($cart_item_key)) {
            return isset($instance->cart_item_price_changes[$cart_item_key]) ? $instance->cart_item_price_changes[$cart_item_key] : array();
        }
        // Return all changes
        else {
            return $instance->cart_item_price_changes;
        }
    }

    /**
     * Maybe change product or variation price of cart item
     *
     * @access public
     * @param float $price
     * @param object $product
     * @return float
     */
    public function maybe_change_price($price, $product)
    {

        // Check if cart has been loaded
        if (!$this->cart_loaded_from_session) {
            return $price;
        }

        // Ensure product is in cart
        if (empty($product->rightpress_in_cart)) {
            return $price;
        }

        // We only need to change final product price here
        if (current_filter() !== 'woocommerce_product_get_price' && current_filter() !== 'woocommerce_product_variation_get_price') {
            return $price;
        }

        // Get cart item key
        $cart_item_key = $product->rightpress_in_cart;

        // Maybe refresh prepared cart item prices
        $this->maybe_refresh_prepared_cart_item_prices($cart_item_key);

        // Check if prepared cart item price exists for current cart item
        if (isset($this->cart_item_price_changes[$cart_item_key])) {

            // Set prepared price
            $price = $this->cart_item_price_changes[$cart_item_key]['price'];
        }

        // Return potentially changed price
        return $price;
    }

    /**
     * Maybe refresh prepared cart item prices
     *
     * @access private
     * @param string $cart_item_key
     * @return void
     */
    private function maybe_refresh_prepared_cart_item_prices($cart_item_key = null)
    {

        $refresh = false;

        // Already refreshing
        if ($this->refreshing_prepared_cart_item_prices) {
            return;
        }

        // Get cart
        if ($cart = RightPress_Help::get_wc_cart()) {

            // Refresh if we don't have price for current cart item key in memory
            if (!isset($this->cart_item_price_changes[$cart_item_key])) {
                $refresh = true;
            }

            // Refresh if environment variables have changed
            if ($this->cart_item_price_changes_environment !== $this->get_environment_variables($cart)) {
                $refresh = true;
            }

            // Check if prepared cart item prices need to be refreshed
            if ($refresh) {

                // Set flag
                $this->refreshing_prepared_cart_item_prices = true;

                // Clear current prepared cart item prices
                $this->cart_item_price_changes = array();
                $this->cart_item_price_changes_environment = array();

                // Let plugins know
                do_action('rightpress_product_price_cart_before_refresh_prepared_cart_item_prices', $cart);

                // Maybe prepare cart item prices
                $this->maybe_prepare_cart_item_prices($cart);

                // Unset flag
                $this->refreshing_prepared_cart_item_prices = false;
            }
        }
    }





}

RightPress_Product_Price_Cart::get_instance();
