<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * Live Product Price Update
 *
 * Note: This is a legacy component in case there are outdated RightPress plugins on site that try to load old
 * component; live product price update functionality has since been moved to rightpress-product-price component
 *
 * @class RightPress_Live_Product_Price_Update
 * @package RightPress
 * @author RightPress
 */
final class RightPress_Live_Product_Price_Update
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
        // Environment variables
        $this->path = trailingslashit(dirname(__FILE__));
        $this->url  = RightPress_Loader::get_component_url('rightpress-live-product-price-update');

        // Print container
        add_action('woocommerce_before_add_to_cart_button', array($this, 'print_container'), 99);

        // Listen for Ajax calls
        add_action('wp_ajax_rightpress_live_product_price_update', array($this, 'update_price'));
        add_action('wp_ajax_nopriv_rightpress_live_product_price_update', array($this, 'update_price'));
    }

    /**
     * Print container
     *
     * @access public
     * @return void
     */
    public function print_container()
    {
        // Only proceed if at least one plugin has registered a filter callback by now
        if (has_filter('rightpress_live_product_price_update')) {

            // Print container
            echo '<dl class="rightpress_live_product_price" style="display: none;"><dt><span class="rightpress_live_product_price_label"></span></dt><dd><span class="price"></span></dd></dl>';

            // Enqueue assets
            $this->enqueue_assets();
        }
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

        // Enqueue jQuery plugins
        RightPress_Loader::load_jquery_plugin('rightpress-helper');
        RightPress_Loader::load_jquery_plugin('rightpress-live-product-update');

        // Enqueue styles
        RightPress_Help::enqueue_or_inject_stylesheet('rightpress-live-product-price-update-styles', $this->url . 'assets/styles.css', $rightpress_version);

        // Enqueue scripts
        wp_enqueue_script('rightpress-live-product-price-update-scripts', $this->url . 'assets/scripts.js', array('jquery'), $rightpress_version);

        // Pass variables
        wp_localize_script('rightpress-live-product-price-update-scripts', 'rightpress_live_product_price_update_vars', array(
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
            define('RIGHTPRESS_LIVE_PRODUCT_PRICE_UPDATE_REQUEST', true);

            // Allow plugins to extract their own data from request
            // NOTE DEVELOPERS: The filter below is for internal use only!
            $custom_keys = apply_filters('rightpress_live_product_price_update_custom_keys', array('rightpress_complete_input_list'));

            // Get request data
            $data = RightPress_Help::get_product_page_ajax_request_data($custom_keys);

            // Load product object
            $object_id = !empty($data['variation_id']) ? $data['variation_id'] : $data['product_id'];
            $product = wc_get_product($object_id);

            // Unable to load product
            if (!$product) {
                throw new Exception('Unable to load product.');
            }

            // Unable to determine variation for variable product
            if ($product->get_type() === 'variable') {
                throw new Exception('Unable to determine product variation.');
            }

            // Define data structure
            $price_data = array(
                'price'     => null,
                'label'     => null,
                'changeset' => array()
            );

            // Allow all plugins to do their own product price adjustments
            // NOTE DEVELOPERS: The filter below is for internal use only!
            $price_data = apply_filters('rightpress_live_product_price_update', $price_data, $product, $data['quantity'], $data['variation_attributes'], $data);

            // No data was provided
            if ($price_data['price'] === null) {

                // Send success response
                echo json_encode(array(
                    'result'    => 'success',
                    'display'   => 0,
                ));
            }
            // Data was provided
            else {

                // Tax adjustment
		if (get_option('woocommerce_tax_display_shop') === 'excl') {
                    $price = wc_get_price_excluding_tax($product, array('price' => $price_data['price']));
		}
                else {
                    $price = wc_get_price_including_tax($product, array('price' => $price_data['price']));
		}

                // Format label
                $label_html = apply_filters('rightpress_live_product_price_update_label_html', (string) $price_data['label']);

                // Format display price
                $price_html = apply_filters('rightpress_live_product_price_update_price_html', wc_price($price), $price, $price_data['label'], $data['product_id'], $data['variation_id'], $data['variation_attributes'], $data['quantity']);

                // Send success response
                echo json_encode(array(
                    'result'        => 'success',
                    'display'       => 1,
                    'price'         => $price,
                    'price_html'    => $price_html,
                    'label_html'    => $label_html,
                    'extra_data'    => apply_filters('rightpress_live_product_price_update_extra_data', array()),
                ));
            }

        } catch (Exception $e) {

            // Send error response
            echo json_encode(array(
                'result'    => 'error',
                'message'   => $e->getMessage(),
            ));
        }

        exit;
    }





}

RightPress_Live_Product_Price_Update::get_instance();
