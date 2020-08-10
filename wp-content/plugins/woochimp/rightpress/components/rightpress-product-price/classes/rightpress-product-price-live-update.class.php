<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * RightPress Shared Product Price Live Update
 *
 * @class RightPress_Product_Price_Live_Update
 * @package RightPress
 * @author RightPress
 */
final class RightPress_Product_Price_Live_Update
{

    // Flag
    private $processing_live_update_request = false;

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

        // Listen for Ajax calls
        add_action('wp_ajax_rightpress_product_price_live_update', array($this, 'update_price'));
        add_action('wp_ajax_nopriv_rightpress_product_price_live_update', array($this, 'update_price'));

        // Continue setup on later init position
        add_action('init', array($this, 'init'), 999);
    }

    /**
     * Continue setup on later init position
     *
     * @access public
     * @return void
     */
    public function init()
    {

        // Get container position hook
        $position_hook = apply_filters('rightpress_product_price_live_update_position_hook', 'woocommerce_before_add_to_cart_button');

        // Print container
        RightPress_Help::add_late_action($position_hook, array($this, 'print_container'));
    }

    /**
     * Print container
     *
     * @access public
     * @return void
     */
    public function print_container()
    {

        // Check if at least one plugin uses this functionality
        if (!apply_filters('rightpress_product_price_live_update_enabled', false)) {
            return;
        }

        // Print container
        echo '<div style="clear: both;"></div><dl class="rightpress_product_price_live_update" style="display: none;"><dt><span class="rightpress_product_price_live_update_label"></span></dt><dd><span class="price"></span></dd></dl>';

        // Enqueue assets
        $this->enqueue_assets();
    }

    /**
     * Enqueue assets
     *
     * @access public
     * @return void
     */
    public function enqueue_assets()
    {

        global $rightpress_version;

        // Enqueue shared assets
        RightPress_Product_Price::enqueue_assets();

        // Enqueue jQuery plugins
        RightPress_Loader::load_jquery_plugin('rightpress-helper');
        RightPress_Loader::load_jquery_plugin('rightpress-live-product-update');

        // Enqueue scripts
        wp_enqueue_script('rightpress-product-price-live-update-scripts', RIGHTPRESS_LIBRARY_URL . '/components/rightpress-product-price/assets/live-update.js', array('jquery'), $rightpress_version);

        // Pass variables
        wp_localize_script('rightpress-product-price-live-update-scripts', 'rightpress_product_price_live_update_vars', array(
            'ajaxurl' => admin_url('admin-ajax.php?rightpress_ajax=1'),
        ));
    }

    /**
     * Update price
     *
     * @access public
     * @return void
     */
    public function update_price()
    {

        try {

            // Set flag
            $this->processing_live_update_request = true;

            // Get request data
            $request_data = RightPress_Product_Price_Live_Update::get_request_data();

            // Load product object
            $object_id = !empty($request_data['variation_id']) ? $request_data['variation_id'] : $request_data['product_id'];
            $product = wc_get_product($object_id);

            // Unable to load product
            if (!$product) {
                throw new Exception('Unable to load product.');
            }

            // Unable to determine variation for variable product
            if ($product->get_type() === 'variable') {
                throw new Exception('Unable to determine product variation.');
            }

            // Product is not available for purchase (price is not set in WooCommerce product settings)
            if ($product->get_price('edit') === '') {

                // Send success response
                echo json_encode(array(
                    'result'    => 'success',
                    'display'   => 0,
                ));
            }
            // Product is available for purchase
            else {

                // Get cart item data
                $cart_item_data = apply_filters('rightpress_product_price_live_update_test_cart_item_data', array(), $product, $request_data);

                // Allow plugins to require default price to be displayed when there are no adjustments applicable
                $always_display = apply_filters('rightpress_product_price_live_update_always_display', false, $product);

                // Run product price test
                $price_data = RightPress_Product_Price_Test::run($product, $request_data['quantity'], $request_data['variation_attributes'], true, $always_display, $cart_item_data);

                // Price test was successful
                if ($price_data !== false) {

                    // Price would be adjusted
                    if ($price_data !== null) {

                        // Format label
                        $label_text = (string) apply_filters('rightpress_product_price_live_update_label', __('Price', 'rightpress'), $product, $price_data);
                        $label_html = apply_filters('rightpress_product_price_live_update_label_html', $label_text, $product, $price_data);

                        // Allow developers to always display quantity, i.e. table with subtotal
                        $always_display_quantity = apply_filters('rightpress_product_price_live_update_always_display_quantity', false, $product, $price_data);

                        // Allow developers to hide subtotal
                        $display_subtotal = apply_filters('rightpress_product_price_live_update_display_subtotal', true, $product, $price_data);

                        // Run price through a WooCommerce product price filter so that 3rd party price adjustments are applied (WCDPD issue #639)
                        $filtered_price = apply_filters('woocommerce_product_get_price', $price_data['price'], $product);

                        // Run price through our own get price filter
                        $filtered_price = apply_filters('rightpress_product_price_live_update_price', $filtered_price, $product);

                        // Get display price html
                        $price_html = RightPress_Product_Price_Display::get_cart_item_product_display_price($product, $price_data, null, $display_subtotal, $always_display_quantity, $filtered_price);

                        // Send success response
                        echo json_encode(array(
                            'result'        => 'success',
                            'display'       => 1,
                            'label_html'    => $label_html,
                            'price_html'    => $price_html,
                            'extra_data'    => apply_filters('rightpress_product_price_live_update_extra_data', array()),
                        ));
                    }
                    // Price would not be adjusted
                    else {

                        // Send success response
                        echo json_encode(array(
                            'result'    => 'success',
                            'display'   => 0,
                        ));
                    }
                }
                // Error occurred during price test
                else {

                    // Send error response
                    echo json_encode(array(
                        'result'    => 'error',
                        'message'   => 'Uknown error.',
                    ));
                }
            }
        }
        catch (Exception $e) {

            // Send error response
            echo json_encode(array(
                'result'    => 'error',
                'message'   => $e->getMessage(),
            ));
        }

        exit;
    }

    /**
     * Check if system is processing product price live update request
     *
     * @access public
     * @return bool
     */
    public static function is_processing_live_update_request()
    {
        $instance = RightPress_Product_Price_Live_Update::get_instance();
        return $instance->processing_live_update_request;
    }

    /**
     * Get request data
     *
     * @access public
     * @return array
     */
    public static function get_request_data()
    {

        // Allow plugins to extract their own data from request
        $custom_keys = apply_filters('rightpress_product_price_live_update_custom_keys', array('rightpress_complete_input_list'));

        // Extract request data
        return RightPress_Help::get_product_page_ajax_request_data($custom_keys);
    }












}

RightPress_Product_Price_Live_Update::get_instance();
