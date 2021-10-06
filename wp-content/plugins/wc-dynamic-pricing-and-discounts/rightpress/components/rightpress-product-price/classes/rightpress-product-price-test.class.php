<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * RightPress Shared Product Price Test
 *
 * Checks what price specific quantity of product would get if it was added to current cart
 *
 * @class RightPress_Product_Price_Test
 * @package RightPress
 * @author RightPress
 */
final class RightPress_Product_Price_Test
{

    // Flags
    private $is_test = false;
    private $product_flagged = false;

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

    }

    /**
     * Run product price test
     *
     * Price test is used in the following circumstances:
     * - Product price live update              - once per request on separate Ajax request
     * - WCDPD promotion countdown timer        - once per request on separate Ajax request
     * - WCDPD auto add free product to cart    - can be multiple calls per request and any request type
     * - WCDPD product prices in shop           - can be multiple calls per request and any request type
     *
     * Return values:
     *   float - price if it would be adjusted
     *   array - extended data if it was requested and price would be adjusted
     *   null  - if price would not be adjusted
     *   false - error ocurred
     *
     * @access public
     * @param object $product
     * @param int $quantity
     * @param array $variation_attributes
     * @param bool $return_data
     * @param bool $return_empty_changes
     * @param array $cart_item_data
     * @param float $custom_price
     * @return float|array|null|false
     */
    public static function run($product, $quantity = 1, $variation_attributes = array(), $return_data = false, $return_empty_changes = false, $cart_item_data = array(), $custom_price = null)
    {

        $result = false;

        // WooCommerce not initialized yet, cart not ready
        if (!did_action('woocommerce_init')) {
            RightPress_Help::doing_it_wrong(__METHOD__, 'Method should not be called before woocommerce_init.', '1.0');
            return $result;
        }

        // Product price is empty
        if ($product->get_price('edit') === '' || $custom_price === '') {
            return $result;
        }

        // Get instance
        $instance = RightPress_Product_Price_Test::get_instance();

        // Set flag
        RightPress_Product_Price_Test::test_started();

        // Get cart items
        $cart_items = RightPress_Help::get_wc_cart_items();

        // Simulate add to cart
        if ($test_cart_items = $instance->simulate_add_to_cart($cart_items, $product, $quantity, $variation_attributes, $cart_item_data)) {

            // Get price changes for cart items
            $test_cart_items_price_changes = RightPress_Product_Price_Changes::get_price_changes_for_cart_items($cart_items, $test_cart_items, $return_empty_changes, $custom_price);

            // Merge test cart items price changes into one price changes array
            $price_changes = RightPress_Product_Price_Test::merge_cart_items_price_changes($test_cart_items_price_changes, $product);

            // Price was adjusted
            if (!empty($price_changes['all_changes']) || $return_empty_changes) {

                $result = $return_data ? $price_changes : $price_changes['price'];
            }
            // Price was not adjusted
            else {

                $result = null;
            }
        }

        // Unset flag
        RightPress_Product_Price_Test::test_ended();

        // Unset product flag
        if ($instance->product_flagged) {
            unset($product->rightpress_in_cart);
            $instance->product_flagged = false;
        }

        // Return result
        return $result;
    }

    /**
     * Simulate add to cart
     *
     * Based on WC_Cart:add_to_cart() version 3.1
     *
     * Returns a list of cart item key / quantity pairs and directly modifies cart items array
     *
     * @access public
     * @param array $cart_items
     * @param object $product
     * @param int $quantity
     * @param array $variation_attributes
     * @param array $cart_item_data
     * @return array|bool
     */
    public function simulate_add_to_cart(&$cart_items, $product, $quantity, $variation_attributes = array(), $cart_item_data = array())
    {

        try {

            // Store cart item keys with quantities to return
            $cart_item_keys_with_quantities = array();

            // Get parent product and variation ids
            $product_id     = RightPress_Help::get_wc_product_absolute_id($product);
            $variation_id   = $product->is_type('variation') ? $product->get_id() : 0;

            // Load cart item data - may be added by other plugins
            $cart_item_data = (array) apply_filters('woocommerce_add_cart_item_data', $cart_item_data, $product_id, $variation_id, $quantity);

            // Allow our plugins to split cart item into multiple cart items
            $extra_cart_items = apply_filters('rightpress_product_price_test_simulate_add_to_cart_extra_items', array(), $product_id, $variation_id, $cart_item_data, $quantity);

            // Decrement quantity of the parent cart item if there are any extra cart items
            foreach ($extra_cart_items as $extra_cart_item) {
                $quantity -= $extra_cart_item['quantity'];
            }

            // Format cart items to add array
            $cart_items_to_add = array(
                array(
                    'quantity'          => $quantity,
                    'cart_item_data'    => $cart_item_data,
                ),
            );

            // Add extra cart items and allow 3rd party plugins to add their data again since it may have been reset by our plugins
            foreach ($extra_cart_items as $extra_cart_item) {
                $cart_items_to_add[] = array(
                    'quantity'          => $extra_cart_item['quantity'],
                    'cart_item_data'    => (array) apply_filters('woocommerce_add_cart_item_data', $extra_cart_item['cart_item_data'], $product_id, $variation_id, $quantity),
                );
            }

            // Iterate over cart items to add
            foreach ($cart_items_to_add as $cart_item_to_add) {

                // Generate cart item key
                $cart_item_key = WC()->cart->generate_cart_id($product_id, $variation_id, $variation_attributes, $cart_item_to_add['cart_item_data']);

                // Item is already in the cart
                if (isset($cart_items[$cart_item_key])) {

                    // Increase quantity
                    $cart_items[$cart_item_key]['quantity'] += $cart_item_to_add['quantity'];
                }
                // Item not yet in cart
                else {

                    // Flag product in cart
                    $product->rightpress_in_cart    = $cart_item_key;
                    $this->product_flagged          = true;

                    // Get price to let currency switchers change product price (WCDPD issue #621)
                    $product->get_price();

                    // Add item after merging with $cart_item_data
                    // Note: the array got filtered by woocommerce_add_cart_item, removed it for Custom Fields compatibility
                    $cart_items[$cart_item_key] = array_merge($cart_item_to_add['cart_item_data'], array(
                        'product_id'    => $product_id,
                        'variation_id'  => $variation_id,
                        'variation'     => $variation_attributes,
                        'quantity'      => $cart_item_to_add['quantity'],
                        'data'          => $product,
                    ));
                }

                // Add cart item key and quantity
                $cart_item_keys_with_quantities[$cart_item_key] = $cart_item_to_add['quantity'];
            }

            // Return cart item keys with quantities
            return $cart_item_keys_with_quantities;
        }
        catch (Exception $e) {

            return false;
        }
    }

    /**
     * Merge multiple cart items price changes arrays into one price changes array
     *
     * This is a special method that merges price changes back to one array after price test methods split test cart item
     * it into multiple test cart items (e.g. different custom field values for different quantity units of the same cart item)
     * thus resulting in multiple price changes arrays (one per cart item)
     *
     * This method must only be used during product price tests
     *
     * @access public
     * @param array $cart_items_price_changes
     * @param object $product
     * @return array
     */
    public static function merge_cart_items_price_changes($cart_items_price_changes, $product)
    {

        // Track price range quantities
        $from = 1;
        $to = 1;

        // Define empty price changes array
        $price_changes = array(
            'prices'            => array(
                'pointers'  => array(),
                'ranges'    => array(),
            ),
            'price'             => null,
            'base_price'        => null,
            'original_price'    => null,
            'new_changes'       => array(),
            'all_changes'       => array(),
        );

        // Extract data from all cart items
        foreach ($cart_items_price_changes as $cart_item_price_changes) {

            // Extract common data
            if (!isset($price_changes['base_price'])) {

                // Pointers
                $pointers_keys = array_keys($cart_item_price_changes['prices']['pointers']);
                $price_changes['prices']['pointers'] = array_fill_keys($pointers_keys, 1);

                // Prices
                $price_changes['base_price']        = $cart_item_price_changes['base_price'];
                $price_changes['original_price']    = $cart_item_price_changes['original_price'];
            }

            // Copy new changes
            foreach ($cart_item_price_changes['new_changes'] as $plugin_key => $plugin_changes) {
                RightPress_Product_Price_Changes::incorporate_new_price_range_plugin_changes($price_changes['new_changes'], $plugin_key, $plugin_changes);
            }

            // Copy all changes
            foreach ($cart_item_price_changes['all_changes'] as $plugin_key => $plugin_changes) {
                RightPress_Product_Price_Changes::incorporate_new_price_range_plugin_changes($price_changes['all_changes'], $plugin_key, $plugin_changes);
            }

            // Extract data from price ranges of current cart item
            foreach ($cart_item_price_changes['prices']['ranges'] as $price_range) {

                // Update "to" quantity
                $to += (RightPress_Product_Price_Breakdown::get_price_range_quantity($price_range) - 1);

                // Add price range
                $price_changes['prices']['ranges'][('to_' . $to)] = array(
                    'from'          => $from,
                    'to'            => $to,
                    'price'         => $price_range['price'],
                    'base_price'    => $price_changes['base_price'],
                    'highest_price' => $price_range['highest_price'],
                    'new_changes'   => $price_range['new_changes'],
                    'all_changes'   => $price_range['all_changes'],
                );

                // Update "from" and "to" for the next range
                $from = $to + 1;
                $to = $from;
            }
        }

        // Calculate average price
        $price_changes['price'] = RightPress_Product_Price_Breakdown::get_price_from_prices_array($price_changes['prices'], $price_changes['original_price'], $product);

        // Return merged price changes array
        return $price_changes;
    }

    /**
     * Check if product pricing test is running
     *
     * @access public
     * @return bool
     */
    public static function is_running()
    {

        $instance = RightPress_Product_Price_Test::get_instance();
        return $instance->is_test;
    }

    /**
     * Test has started
     *
     * @access public
     * @return void
     */
    public static function test_started()
    {

        $instance = RightPress_Product_Price_Test::get_instance();
        $instance->is_test = true;
    }

    /**
     * Test has ended
     *
     * @access public
     * @return void
     */
    public static function test_ended()
    {

        $instance = RightPress_Product_Price_Test::get_instance();
        $instance->is_test = false;
    }

    /**
     * Get price data for Ajax tools
     *
     * Note: This method sets running_custom_calculations flag and does not unset it since Ajax calls are expected
     * to only do one specific task which requires this flag to be set
     *
     * @access public
     * @param array $cart_item_data
     * @param bool $return_empty_changes
     * @return array|null|false
     */
    public static function get_price_data_for_ajax_tools($cart_item_data = array(), $return_empty_changes = false)
    {

        // Set flag
        RightPress_Product_Price::start_running_custom_calculations();

        // Get request data
        $request_data = RightPress_Product_Price::get_request_data_for_ajax_tools();

        // Load product object
        $object_id = !empty($request_data['variation_id']) ? $request_data['variation_id'] : $request_data['product_id'];
        $product = wc_get_product($object_id);

        // Unable to load product object
        if (!$product) {
            return false;
        }

        // Unable to determine variation for variable product
        if ($product->get_type() === 'variable') {
            return false;
        }

        // Product is available for purchase
        if ($product->get_price('edit') !== '') {

            // Get cart item data
            $cart_item_data = apply_filters('rightpress_product_price_test_cart_item_data', $cart_item_data, $product, $request_data);

            // Run product price test and return results
            return RightPress_Product_Price_Test::run($product, $request_data['quantity'], $request_data['variation_attributes'], true, $return_empty_changes, $cart_item_data);
        }
        // Product is not available for purchase (price is not set in WooCommerce product settings)
        else {

            return null;
        }
    }





}

RightPress_Product_Price_Test::get_instance();
