<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * RightPress Shared Product Price Changes
 *
 * @class RightPress_Product_Price_Changes
 * @package RightPress
 * @author RightPress
 */
final class RightPress_Product_Price_Changes
{

    // Flags
    private $getting_price_changes_for_cart_items = false;

    // Store some data in memory
    private $intermediate_reference_prices = array();

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
     * =================================================================================================================
     * MAIN PRICE CHANGES FLOW
     * =================================================================================================================
     */

    /**
     * Get price changes for cart items
     *
     * Notes:
     *  - This method can be called multiple times during the same request for the same cart - plugins must return price changes repeatedly for every filter call
     *  - This method can be called for both real cart ($cart is set) and simulated cart during price tests (in which case $test_cart_items is not empty)
     *  - If cart item key is not set in a resulting array then that cart item has no changes to prices
     *
     * @access public
     * @param array $cart_items
     * @param array $test_cart_items
     * @param bool $return_empty_changes
     * @param float $custom_price
     * @return array
     */
    public static function get_price_changes_for_cart_items($cart_items, $test_cart_items = array(), $return_empty_changes = false, $custom_price = null)
    {

        // Get instance
        $instance = RightPress_Product_Price_Changes::get_instance();

        // Set getting price changes flag
        $instance->getting_price_changes_for_cart_items = true;

        // Prepare price changes array for processing
        $price_changes = RightPress_Product_Price_Changes::prepare_price_changes_array_for_processing($cart_items, $custom_price);

        // FIRST STAGE - Get price changes
        foreach (apply_filters('rightpress_product_price_cart_item_price_changes_first_stage_callbacks', array()) as $callback) {
            $price_changes = RightPress_Product_Price_Changes::get_price_changes_from_callback_with_alternatives($price_changes, $callback, $cart_items, $test_cart_items);
        }

        // Set intermediate reference prices
        $instance->set_intermediate_reference_prices($price_changes, $cart_items);

        // Let second stage plugins prepare their price changes
        do_action('rightpress_product_price_prepare_second_stage_cart_item_price_changes', $cart_items);

        // Finalize base price selection and incorporate selected alternative
        // Note: Currently only one alternative price by WCDPD is expected and alternative price is selected if WCDPD has adjustments to apply for a particular cart item
        $price_changes = RightPress_Product_Price_Changes::finalize_base_price_selection($price_changes);

        // SECOND STAGE - Get price changes
        foreach (apply_filters('rightpress_product_price_cart_item_price_changes_second_stage_callbacks', array()) as $callback) {
            $price_changes = RightPress_Product_Price_Changes::get_price_changes_from_callback($price_changes, $callback, $cart_items, $test_cart_items);
        }

        // Iterate over price changes
        foreach ($price_changes as $cart_item_key => $cart_item_changes) {

            // Price test in progress
            if (!empty($test_cart_items)) {

                // Unset changes to other cart items
                if (!isset($test_cart_items[$cart_item_key])) {
                    unset($price_changes[$cart_item_key]);
                    continue;
                }

                // Unset changes to non-test quantity units
                $price_changes[$cart_item_key]['prices'] = RightPress_Product_Price_Breakdown::filter_prices_array_for_test_quantity($price_changes[$cart_item_key]['prices'], $test_cart_items[$cart_item_key]);
            }

            // Incorporate new changes for cart item
            RightPress_Product_Price_Changes::incorporate_new_changes_for_cart_item($price_changes[$cart_item_key]['prices'], $price_changes[$cart_item_key]);

            // Remove cart item from array if it does not have any changes at all
            if (empty($price_changes[$cart_item_key]['all_changes']) && !$return_empty_changes) {
                unset($price_changes[$cart_item_key]);
                continue;
            }

            // Get price from prices array
            $price = RightPress_Product_Price_Breakdown::get_price_from_prices_array($price_changes[$cart_item_key]['prices'], $price_changes[$cart_item_key]['original_price'], $cart_items[$cart_item_key]['data'], $cart_items[$cart_item_key]);

            // Allow developers to override and set to price changes array
            $price_changes[$cart_item_key]['price'] = apply_filters('rightpress_product_price_changes_price_to_set', $price, $cart_item_key, $price_changes, $cart_items);
        }

        // Cleanup
        $instance->intermediate_reference_prices = array();

        // Unset getting price changes flag
        $instance->getting_price_changes_for_cart_items = false;

        // Return price changes
        return $price_changes;
    }

    /**
     * Incorporate new changes for cart item
     *
     * Note: This method is used in this class but it may also be used by individual plugins
     * when calculating their own changes for their custom purposes (in which case $cart_item_changes is null)
     *
     * @access public
     * @param $prices
     * @param $cart_item_changes
     * @return void
     */
    public static function incorporate_new_changes_for_cart_item(&$prices, &$cart_item_changes = null)
    {

        // Iterate over price ranges of current cart item
        foreach ($prices['ranges'] as $price_range_index => $price_range) {

            // Filter out empty plugin changes arrays
            $price_range_new_changes = array_filter($price_range['new_changes']);

            // Incorporate new price range changes
            foreach ($price_range_new_changes as $plugin_key => $plugin_changes) {

                // Add to price range all changes array
                RightPress_Product_Price_Changes::incorporate_new_price_range_plugin_changes($prices['ranges'][$price_range_index]['all_changes'], $plugin_key, $plugin_changes);

                // Check if cart item changes is defined
                if ($cart_item_changes !== null) {

                    // Add to price changes new changes array
                    RightPress_Product_Price_Changes::incorporate_new_price_range_plugin_changes($cart_item_changes['new_changes'], $plugin_key, $plugin_changes);

                    // Add to price changes all changes array
                    RightPress_Product_Price_Changes::incorporate_new_price_range_plugin_changes($cart_item_changes['all_changes'], $plugin_key, $plugin_changes);
                }
            }
        }
    }

    /**
     * Incorporate new price range plugin changes
     *
     * @access public
     * @param array $destination_array
     * @param string $plugin_key
     * @param array $plugin_changes
     * @return void
     */
    public static function incorporate_new_price_range_plugin_changes(&$destination_array, $plugin_key, $plugin_changes)
    {
        // Plugin changes array does not exist in destination array yet
        if (!isset($destination_array[$plugin_key])) {
            $destination_array[$plugin_key] = array();
        }

        // Merge new plugin changes with existing changes in destination array
        $destination_array[$plugin_key] = array_merge($destination_array[$plugin_key], $plugin_changes);
    }

    /**
     * Prepare price changes array for processing
     *
     * Note: This must only be called from RightPress_Product_Price_Changes::get_price_changes_for_cart_items(),
     * otherwise there could be problems with base price selection by plugins
     *
     * WARNING! TODO! If changes are made to format of this array, they must also be made in RightPress_Product_Price_Test::merge_cart_items_price_changes()
     *
     * @access private
     * @param array $cart_items
     * @param float $custom_price
     * @return array
     */
    private static function prepare_price_changes_array_for_processing($cart_items, $custom_price = null)
    {

        $price_changes = array();

        // Iterate over cart items
        foreach ($cart_items as $cart_item_key => $cart_item) {

            // Get current product price as set on product or use custom price if provided
            $base_price = $original_price = (float) ($custom_price !== null ? $custom_price : $cart_item['data']->get_price('edit'));

            // Format price changes array for current cart item
            $price_changes[$cart_item_key] = array(

                // Commented items are added to the main array after base price selection is finalized before second stage
                // 'prices'            => $prices,         // See RightPress_Product_Price_Breakdown::generate_prices_array() for details
                // 'price'             => $base_price,     // Single price for all quantity units or average price of prices, only updated at the very end of the process, plugins must work exclusively with the prices array
                // 'base_price'        => $base_price,     // Price that calculations was based on - may be reset to "regular price" (may include 3rd party changes)

                // Note: 'new_changes' and 'all_changes' separation is legacy handling that was important before WCDPD issue #639
                'original_price'    => $original_price, // Price of the cart item's product just like it was on first call (may include 3rd party changes)
                'new_changes'       => array(),         // Changes that were applicable to prices during current call, aggregated from price ranges new changes arrays, cleared at the beginning of each call
                'all_changes'       => array(),         // All changes that were applicable to prices, new changes are merged into all changes array at the end of each call
                'alternatives'      => array(),         // This stores alternative price calculations during the first stage, when base price selection is finalized one alternative is chosen, its contents are merged with the main array and this element is nulled
            );

            // Format base price candidates array
            $base_price_key         = RightPress_Product_Price::get_price_key($base_price);
            $base_price_candidates  = array($base_price_key => $base_price);

            // Allow plugins to add base price candidates
            $base_price_candidates = apply_filters('rightpress_product_price_cart_item_base_price_candidates', $base_price_candidates, $cart_item['data'], $cart_item_key, $cart_item);

            // Iterate over base price candidates
            foreach ($base_price_candidates as $base_price_candidate_key => $base_price_candidate) {

                // Generate prices array
                $prices = RightPress_Product_Price_Breakdown::generate_prices_array($base_price_candidate, $cart_item['quantity'], $cart_item['data']);

                // Set as alternative
                // Note: Currently it is important to preserve initial order of base price alternatives as the first element of the array is considered to be the default one
                $price_changes[$cart_item_key]['alternatives'][$base_price_candidate_key] = array(
                    'prices'        => $prices,
                    'price'         => $base_price_candidate,
                    'base_price'    => $base_price_candidate,
                );
            }
        }

        return $price_changes;
    }

    /**
     * Finalize base price selection and incorporate selected alternative
     *
     * @access private
     * @param array $price_changes
     * @return array
     */
    private static function finalize_base_price_selection($price_changes)
    {

        // Iterate over price changes
        foreach ($price_changes as $cart_item_key => $cart_item_changes) {

            // Get default base price key for current cart item
            reset($cart_item_changes['alternatives']);
            $default_base_price_key = key($cart_item_changes['alternatives']);

            // Allow plugins to change selected cart item base price key
            $base_price_key = apply_filters('rightpress_product_price_selected_cart_item_base_price_key', $default_base_price_key, array_keys($cart_item_changes['alternatives']), $cart_item_key);

            // Move selected alternative data to the main array and unset alternatives array
            $price_changes[$cart_item_key] = array_merge($cart_item_changes['alternatives'][$base_price_key], $cart_item_changes);
            unset($price_changes[$cart_item_key]['alternatives']);
        }

        // Return price changes array with incorporated selected alternative
        return $price_changes;
    }

    /**
     * Set intermediate reference prices
     *
     * @access private
     * @param array $price_changes
     * @param array $cart_items
     * @return void
     */
    private static function set_intermediate_reference_prices($price_changes, $cart_items)
    {

        // Get instance
        $instance = RightPress_Product_Price_Changes::get_instance();

        // Iterate over price changes
        foreach ($price_changes as $cart_item_key => $cart_item_price_changes) {

            // Get last alternative array in list
            // Note: Currently we only expect one (default) or two (WCDPD regular price override) alternatives and the last one
            // is always fit for the only plugin (WCDPD) that is currently in second stage and would use intermediate reference prices
            $last_alternative = array_pop($cart_item_price_changes['alternatives']);

            // Set intermediate reference price for current cart item
            $instance->intermediate_reference_prices[$cart_item_key] = RightPress_Product_Price_Breakdown::get_price_from_prices_array($last_alternative['prices'], $cart_item_price_changes['original_price'], $cart_items[$cart_item_key]['data'], $cart_items[$cart_item_key], true);
        }
    }

    /**
     * Get intermediate reference price for cart item
     *
     * @access public
     * @param string $cart_item_key
     * @return float|null
     */
    public static function get_intermediate_reference_price($cart_item_key)
    {

        // Get instance
        $instance = RightPress_Product_Price_Changes::get_instance();

        // Return intermediate reference price if available
        return isset($instance->intermediate_reference_prices[$cart_item_key]) ? $instance->intermediate_reference_prices[$cart_item_key] : null;
    }

    /**
     * Get intermediate reference price for cart item
     *
     * Legacy method, in case WCCF loads new library while old version of WCDPD is present)
     *
     * @access public
     * @param string $cart_item_key
     * @return float|null
     */
    public static function get_second_stage_reference_price($cart_item_key)
    {

        return RightPress_Product_Price_Changes::get_intermediate_reference_price($cart_item_key);
    }

    /**
     * Get price changes from callback
     *
     * @access private
     * @param array $price_changes
     * @param mixed $callback
     * @param array $cart_items
     * @param array $test_cart_items
     * @return array
     */
    private static function get_price_changes_from_callback($price_changes, $callback, $cart_items, $test_cart_items = array())
    {

        // Get prices changes from current plugin
        $price_changes = call_user_func($callback, $price_changes, $cart_items, $test_cart_items);

        // Post callback cart item price changes processing
        foreach ($price_changes as $cart_item_key => $cart_item_changes) {
             RightPress_Product_Price_Changes::post_callback_cart_item_price_changes_processing($price_changes[$cart_item_key], $cart_items, $cart_item_key);
        }

        // Return price changes
        return $price_changes;
    }

    /**
     * Get price changes from callback with alternatives
     *
     * @access private
     * @param array $price_changes
     * @param mixed $callback
     * @param array $cart_items
     * @param array $test_cart_items
     * @return array
     */
    private static function get_price_changes_from_callback_with_alternatives($price_changes, $callback, $cart_items, $test_cart_items = array())
    {

        // Get prices changes from current plugin
        $price_changes = call_user_func($callback, $price_changes, $cart_items, $test_cart_items);

        // Post callback cart item price changes processing
        foreach ($price_changes as $cart_item_key => $cart_item_changes) {
            foreach ($cart_item_changes['alternatives'] as $base_price_candidate_key => $alternative_changes) {
                RightPress_Product_Price_Changes::post_callback_cart_item_price_changes_processing($price_changes[$cart_item_key]['alternatives'][$base_price_candidate_key], $cart_items, $cart_item_key);
            }
        }

        // Return price changes
        return $price_changes;
    }

    /**
     * Post callback cart item price changes processing
     *
     * Note: $cart_item_changes may actually be one of the alternative changes, therefore we can only work with
     * data that is set on alternative changes arrays
     *
     * @access private
     * @param array $cart_item_changes
     * @param array $cart_items
     * @param string $cart_item_key
     * @return void
     */
    private static function post_callback_cart_item_price_changes_processing(&$cart_item_changes, $cart_items, $cart_item_key)
    {

        // Reset price ranges sort order
        RightPress_Product_Price_Breakdown::reset_price_ranges_sort_order($cart_item_changes['prices']);

        // Update highest price of each price range
        foreach ($cart_item_changes['prices']['ranges'] as $price_range_key => $price_range) {
            if ($price_range['price'] > $price_range['highest_price']) {
                $cart_item_changes['prices']['ranges'][$price_range_key]['highest_price'] = $price_range['price'];
            }
        }
    }


    /**
     * =================================================================================================================
     * CART ITEM SORTING
     * =================================================================================================================
     */

    /**
     * Sort cart items by price
     *
     * @access public
     * @param array $cart_items
     * @param string $sort_order
     * @param bool $use_reference_price
     * @return array
     */
    public static function sort_cart_items_by_price($cart_items = null, $sort_order = 'ascending', $use_reference_price = false)
    {

        // Get cart items if not passed in
        if ($cart_items === null) {
            $cart_items = RightPress_Help::get_wc_cart_items();
        }

        // Sort cart items
        $sort_comparison_method = 'sort_cart_items_by_price_' . $sort_order . '_comparison';
        RightPress_Help::stable_uasort($cart_items, array('RightPress_Product_Price_Changes', $sort_comparison_method), array('use_reference_price' => $use_reference_price));

        // Return sorted cart items
        return $cart_items;
    }

    /**
     * Sort cart items by price ascending comparison method
     *
     * @access public
     * @param mixed $a
     * @param mixed $b
     * @param array $params
     * @return bool
     */
    public static function sort_cart_items_by_price_ascending_comparison($a, $b, $params = array())
    {

        return RightPress_Product_Price_Changes::sort_cart_items_by_price_comparison($a, $b, 'ascending', $params);
    }

    /**
     * Sort cart items by price descending comparison method
     *
     * @access public
     * @param mixed $a
     * @param mixed $b
     * @param array $params
     * @return bool
     */
    public static function sort_cart_items_by_price_descending_comparison($a, $b, $params = array())
    {

        return RightPress_Product_Price_Changes::sort_cart_items_by_price_comparison($a, $b, 'descending', $params);
    }

    /**
     * Sort cart items by price comparison method
     *
     * @access public
     * @param mixed $a
     * @param mixed $b
     * @param string $sort_order
     * @param array $params
     * @return bool
     */
    public static function sort_cart_items_by_price_comparison($a, $b, $sort_order, $params = array())
    {

        // Get instance
        $instance = RightPress_Product_Price_Changes::get_instance();

        // Get cart item prices
        $price_a = (!empty($params['use_reference_price']) && isset($instance->intermediate_reference_prices[$a['key']])) ? $instance->intermediate_reference_prices[$a['key']] : (float) $a['data']->get_price();
        $price_b = (!empty($params['use_reference_price']) && isset($instance->intermediate_reference_prices[$b['key']])) ? $instance->intermediate_reference_prices[$b['key']] : (float) $b['data']->get_price();

        // Prices are the same
        if (!RightPress_Product_Price::prices_differ($price_a, $price_b)) {
            return 0;
        }

        // Compare prices
        if (($price_a - $price_b) < 0) {
            return ($sort_order === 'ascending' ? -1 : 1);
        }
        else {
            return ($sort_order === 'ascending' ? 1 : -1);
        }
    }


    /**
     * =================================================================================================================
     * OTHER METHODS
     * =================================================================================================================
     */

    /**
     * Get highest price from cart item price changes
     *
     * Either gets highest price for a single price range or for the whole changes set
     *
     * @access public
     * @param array $price_data
     * @param array $price_range
     * @return float
     */
    public static function get_highest_price_from_cart_item_price_changes($price_data, $price_range = array())
    {

        // Price range is specified
        if (!empty($price_range)) {

            $price_range_alternative = $price_range['highest_price'];
        }
        // Price range is not specified
        else {

            $quantity = 0;

            // Calculate total quantity
            foreach ($price_data['prices']['ranges'] as $price_range) {
                $quantity += RightPress_Product_Price_Breakdown::get_price_range_quantity($price_range);
            }

            // Get highest prices subtotal from cart item price changes
            $subtotal = RightPress_Product_Price_Changes::get_highest_prices_subtotal_from_cart_item_price_changes($price_data);

            // Calculate average highest price for price range
            $price_range_alternative = ($subtotal / $quantity);
        }

        // Get potential highest prices
        $highest_price_alternatives = array(
            $price_range_alternative,
            $price_data['original_price'],
            $price_data['base_price'],
        );

        // Select highest price and return
        return max($highest_price_alternatives);
    }

    /**
     * Get highest prices subtotal from cart item price changes
     *
     * @access public
     * @param array $price_data
     * @return float
     */
    public static function get_highest_prices_subtotal_from_cart_item_price_changes($price_data)
    {

        $subtotal = 0.0;

        // Iterate over price ranges
        foreach ($price_data['prices']['ranges'] as $price_range) {

            // Get price range quantity
            $price_range_quantity = RightPress_Product_Price_Breakdown::get_price_range_quantity($price_range);

            // Add price range highest price subtotal
            $subtotal += ($price_range['highest_price'] * $price_range_quantity);
        }

        return $subtotal;
    }





}

RightPress_Product_Price_Changes::get_instance();
