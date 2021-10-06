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

    // Default position hook
    private $default_position_hook = 'woocommerce_before_add_to_cart_button';

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

        // Get position hook
        $position_hook = $this->get_position_hook();

        // Set position hook to default position hook if element is to replace main WooCommerce price element
        if ($position_hook === 'rightpress_replace_wc_price') {
            $position_hook = $this->default_position_hook;
        }

        // Print container
        RightPress_Help::add_late_action($position_hook, array($this, 'print_container'));
    }

    /**
     * Get position hook
     *
     * @access public
     * @return string
     */
    public function get_position_hook()
    {

        // Allow plugins/developers to override default position hook and return
        return apply_filters('rightpress_product_price_live_update_position_hook', $this->default_position_hook);
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

        // Print container only if not replacing default WooCommerce price element
        if ($this->get_position_hook() !== 'rightpress_replace_wc_price') {
            echo '<div class="rightpress_clear_both"></div><dl class="rightpress_product_price_live_update" style="display: none;"><dt><span class="rightpress_product_price_live_update_label"></span></dt><dd><span class="price rightpress_product_price_live_update_price"></span></dd></dl>';
        }

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
            'ajaxurl'           => admin_url('admin-ajax.php?rightpress_ajax=1'),
            'replace_wc_price'  => ($this->get_position_hook() === 'rightpress_replace_wc_price'),
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

            // Get request data
            $request_data = RightPress_Product_Price::get_request_data_for_ajax_tools();

            // Load product object
            $object_id = !empty($request_data['variation_id']) ? $request_data['variation_id'] : $request_data['product_id'];
            $product = wc_get_product($object_id);

            // Unable to load product object
            if (!$product) {
                throw new Exception('Unable to load product.');
            }

            // Get cart item data
            $cart_item_data = apply_filters('rightpress_product_price_live_update_test_cart_item_data', array(), $product, $request_data);

            // Allow plugins to require default price to be displayed when there are no adjustments applicable
            $always_display = apply_filters('rightpress_product_price_live_update_always_display', false, $product);

            // Get price data
            $price_data = RightPress_Product_Price_Test::get_price_data_for_ajax_tools($cart_item_data, $always_display);

            // Error occurred
            if ($price_data === false) {

                // Send response
                echo json_encode(array(
                    'result'    => 'error',
                    'message'   => 'Uknown error.',
                ));
            }
            // Price would not be adjusted
            else if ($price_data === null) {

                // Send response
                echo json_encode(array(
                    'result'    => 'success',
                    'display'   => 0,
                ));
            }
            // Price would be adjusted
            else {

                // Format label
                $label_text = (string) apply_filters('rightpress_product_price_live_update_label', esc_html__('Price', 'rightpress'), $product, $price_data);
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
     * Get request data
     *
     * Proxy for RightPress_Product_Price::get_request_data_for_ajax_tools()
     *
     * Note: Do not remove this method from this location as it is called by several plugins which may be of different versions
     *
     * @access public
     * @return array
     */
    public static function get_request_data()
    {

        return RightPress_Product_Price::get_request_data_for_ajax_tools();
    }





}

RightPress_Product_Price_Live_Update::get_instance();
