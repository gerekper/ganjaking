<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * RightPress Shared Product Price Component
 *
 * @class RightPress_Product_Price
 * @package RightPress
 * @author RightPress
 */
final class RightPress_Product_Price
{

    // Singleton control
    protected static $instance = false; public static function get_instance() { return self::$instance ? self::$instance : (self::$instance = new self()); }

    // Save references to products for access to price caches
    private $reference_products = array();

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        // Flag products in cart
        add_filter('woocommerce_add_cart_item', array($this, 'flag_product_in_cart'), 1);
        add_filter('woocommerce_get_cart_item_from_session', array($this, 'flag_product_in_cart'), 1);

        // WPML Multi Currency support
        add_filter('wcml_multi_currency_ajax_actions', array($this, 'wcml_multi_currency_ajax_actions'));

        // Continue setup on init
        // Note: All plugins using this component must set up their callbacks during init before position 20
        add_action('init', array($this, 'init'), 20);
    }

    /**
     * Continue setup on init
     *
     * Note: Keep this functionality on init as plugins need time to load settings etc
     *
     * @access public
     * @return void
     */
    public function init()
    {

        // Load classes
        require_once __DIR__ . '/classes/rightpress-product-price-background-refresh.class.php';
        require_once __DIR__ . '/classes/rightpress-product-price-breakdown.class.php';
        require_once __DIR__ . '/classes/rightpress-product-price-cart.class.php';
        require_once __DIR__ . '/classes/rightpress-product-price-changes.class.php';
        require_once __DIR__ . '/classes/rightpress-product-price-display.class.php';
        require_once __DIR__ . '/classes/rightpress-product-price-exception.class.php';
        require_once __DIR__ . '/classes/rightpress-product-price-live-update.class.php';
        require_once __DIR__ . '/classes/rightpress-product-price-shop.class.php';
        require_once __DIR__ . '/classes/rightpress-product-price-test.class.php';

        // This class calls other classes directly from constructor so we load it last
        require_once __DIR__ . '/classes/rightpress-product-price-router.class.php';
    }

    /**
     * Flag product in cart
     *
     * @access public
     * @param array $cart_item_data
     * @return array
     */
    public function flag_product_in_cart($cart_item_data)
    {

        $cart_item_data['data']->rightpress_in_cart = $cart_item_data['key'];
        return $cart_item_data;
    }


    /**
     * =================================================================================================================
     * ASSETS
     * =================================================================================================================
     */

    /**
     * Enqueue component assets
     *
     * @access public
     * @return void
     */
    public static function enqueue_assets()
    {

        global $rightpress_version;

        // Enqueue styles
        RightPress_Help::enqueue_or_inject_stylesheet('rightpress-product-price-styles', RIGHTPRESS_LIBRARY_URL . '/components/rightpress-product-price/assets/styles.css', $rightpress_version);
    }


    /**
     * =================================================================================================================
     * ROUNDING AND DECIMALS
     * =================================================================================================================
     */

    /**
     * Round product price
     *
     * @access public
     * @param float $price
     * @param int $decimals
     * @param bool $skip_default_rounding
     * @return float
     */
    public static function round($price, $decimals = null, $skip_default_rounding = false)
    {

        // Get decimals
        $decimals = RightPress_Product_Price::get_price_decimals($decimals);

        // Maybe apply default rounding
        $rounded_price = $skip_default_rounding ? $price : round($price, $decimals);

        // Allow developers to do their own rounding
        return apply_filters('rightpress_product_price_rounded_price', $rounded_price, $price, $decimals);
    }

    /**
     * Get product price decimals
     *
     * @access public
     * @param int $decimals
     * @return int
     */
    public static function get_price_decimals($decimals = null)
    {

        // Get decimals
        $decimals = isset($decimals) ? $decimals : wc_get_price_decimals();

        // Allow developers to override
        return apply_filters('rightpress_product_price_decimals', $decimals);
    }

    /**
     * Get product display price decimals
     *
     * @access public
     * @param int $decimals
     * @return int
     */
    public static function get_display_price_decimals($decimals = null)
    {

        return apply_filters('rightpress_product_price_display_decimals', RightPress_Product_Price::get_price_decimals($decimals));
    }


    /**
     * =================================================================================================================
     * PRICE COMPARISON
     * =================================================================================================================
     */

    /**
     * Check if prices differ in a float-safe way
     *
     * @access public
     * @param float|string $first_price
     * @param float|string $second_price
     * @return bool
     */
    public static function prices_differ($first_price, $second_price)
    {

        // Special case - one of the prices is an empty string
        if (($first_price === '' && $second_price !== '') || ($first_price !== '' && $second_price === '')) {
            return true;
        }

        return (abs((float) $first_price - (float) $second_price) > 0.000001);
    }

    /**
     * Check if first price is bigger than second price
     *
     * @access public
     * @param float $first_price
     * @param float $second_price
     * @return bool
     */
    public static function price_is_bigger_than($first_price, $second_price)
    {

        return (((float) $first_price - (float) $second_price) > 0.000001);
    }

    /**
     * Check if first price is smaller than second price
     *
     * @access public
     * @param float $first_price
     * @param float $second_price
     * @return bool
     */
    public static function price_is_smaller_than($first_price, $second_price)
    {

        return (((float) $second_price - (float) $first_price) > 0.000001);
    }

    /**
     * Check if price is zero in a float-safe way
     *
     * @access public
     * @param float $price
     * @return bool
     */
    public static function price_is_zero($price)
    {

        return ((float) $price < 0.000001);
    }


    /**
     * =================================================================================================================
     * PRODUCT REFERENCES
     * =================================================================================================================
     */

    /**
     * Set reference product
     *
     * @access public
     * @param WC_Product $product
     * @return void
     */
    public static function set_reference_product($product)
    {

        // Get instance
        $instance = RightPress_Product_Price::get_instance();

        // Check product
        if (is_a($product, 'WC_Product') && $product->get_id()) {

            // Check if reference product is missing
            if (!isset($instance->reference_products[$product->get_id()])) {

                // Set reference product
                $instance->reference_products[$product->get_id()] = $product;
            }
        }
    }

    /**
     * Get reference product
     *
     * @access public
     * @param WC_Product|int $product
     * @return WC_Product|false
     */
    public static function get_reference_product($product)
    {

        // Get instance
        $instance = RightPress_Product_Price::get_instance();

        // Get product id
        $product_id = is_a($product, 'WC_Product') ? $product->get_id() : $product;

        // Reference product is not set
        if (!isset($instance->reference_products[$product_id])) {

            // Load reference product
            if ($reference_product = wc_get_product($product_id)) {

                // Set reference product
                RightPress_Product_Price::set_reference_product($reference_product);
            }
        }

        // Return either a valid reference product or false
        return isset($instance->reference_products[$product_id]) ? $instance->reference_products[$product_id] : false;
    }


    /**
     * =================================================================================================================
     * OTHER METHODS
     * =================================================================================================================
     */

    /**
     * Get price key
     *
     * @access public
     * @param float $price
     * @return string
     */
    public static function get_price_key($price)
    {

        $price_key = '';

        if ($price !== '') {
            $price_key = number_format($price, RightPress_Product_Price::get_price_decimals());
        }

        return $price_key;
    }

    /**
     * WPML Multi Currency support
     *
     * @access public
     * @param array $hooks
     * @return array
     */
    public function wcml_multi_currency_ajax_actions($hooks)
    {

        // Add our ajax hook
        $hooks[] = 'rightpress_product_price_live_update';

        return $hooks;
    }

    /**
     * Subtract tax from product prices and checkout fees by tax class when WooCommerce adds taxes on top of the amount
     *
     * @access public
     * @param float $amount
     * @param string $tax_class
     * @return float
     */
    public static function maybe_subtract_tax_from_amount($amount, $tax_class)
    {

        $result = $amount;

        // Check if tax class is set
        if ($tax_class !== false && $tax_class !== null) {

            // Check if WooCommerce product prices include tax
            if (wc_prices_include_tax()) {

                // Calculate tax amount
                $tax_amount = array_sum(WC_Tax::calc_inclusive_tax($amount, WC_Tax::get_rates($tax_class)));

                // Subtract tax from amount
                $result -= $tax_amount;
            }
        }

        return $result;
    }





}

RightPress_Product_Price::get_instance();
