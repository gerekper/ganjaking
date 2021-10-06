<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * RightPress Shared Product Price Router
 *
 * @class RightPress_Product_Price_Router
 * @package RightPress
 * @author RightPress
 */
final class RightPress_Product_Price_Router
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

        // Check if functionality of this class is used by any plugin
        if (!RightPress_Product_Price_Shop::is_used() && !RightPress_Product_Price_Cart::is_used()) {
            return;
        }

        // Set up price hooks
        RightPress_Help::add_early_filter('woocommerce_product_get_price', array($this, 'route_get_price_call'), 2);
        RightPress_Help::add_early_filter('woocommerce_product_get_sale_price', array($this, 'route_get_price_call'), 2);
        RightPress_Help::add_early_filter('woocommerce_product_get_regular_price', array($this, 'route_get_price_call'), 2);
        RightPress_Help::add_early_filter('woocommerce_product_variation_get_price', array($this, 'route_get_price_call'),  2);
        RightPress_Help::add_early_filter('woocommerce_product_variation_get_sale_price', array($this, 'route_get_price_call'), 2);
        RightPress_Help::add_early_filter('woocommerce_product_variation_get_regular_price', array($this, 'route_get_price_call'), 2);
    }

    /**
     * Route get price call
     *
     * @access public
     * @param float $price
     * @param object $product
     * @return float|string
     */
    public function route_get_price_call($price, $product)
    {

        // Set reference product
        RightPress_Product_Price::set_reference_product($product);

        // Skip variable products
        // Note: this does not affect individual variations
        if ($product->is_type('variable')) {
            return $price;
        }

        // Skip when system is running custom calculations
        if (RightPress_Product_Price::is_running_custom_calculations()) {
            return $price;
        }

        // Cart item product
        if (!empty($product->rightpress_in_cart)) {

            return RightPress_Product_Price_Cart::get_instance()->maybe_change_price($price, $product);
        }
        // Not cart item product
        else {

            return RightPress_Product_Price_Shop::get_instance()->maybe_change_price($price, $product);
        }
    }





}

RightPress_Product_Price_Router::get_instance();
