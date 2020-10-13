<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Warranty_Shortcodes {

    public function __construct() {
        // Account Shortcode
        add_shortcode( 'warranty_request', array($this, 'render_warranty_request_shortcode') );

        // Generic Return Form Shortcode
        add_shortcode( 'warranty_return_form', array($this, 'render_return_form_shortcode') );
    }

    /**
     * Returns the content for the shortcode [warranty_request]
     * @return string The HTML content
     */
    function render_warranty_request_shortcode() {
        global $current_user, $wpdb, $woocommerce;

        if ( isset($_REQUEST['order']) ) {
            $order_id = trim($_REQUEST['order']);
        } else {
            return __('No order selected.', 'wc_warranty');
        }

        if (! is_numeric($order_id) || ! wc_get_order( $order_id ) ||
            ! is_user_logged_in() || ! current_user_can( 'view_order', intval( $order_id ) ) ) {
        	return __('No order selected.', 'wc_warranty');
        }

        $product_id = false;

        if ( isset($_REQUEST['product_id']) ) {
            $product_id = intval($_REQUEST['product_id']);
        }

        ob_start();
        include WooCommerce_Warranty::$base_path .'templates/shortcode-content.php';
        return ob_get_clean();
    }

    /**
     * Generates and returns the form for generic return requests
     * @return string
     */
    function render_return_form_shortcode() {
        global $current_user, $wpdb;

        $defaults = array(
            'first_name'    => (!empty($current_user->billing_first_name)) ? $current_user->billing_first_name : '',
            'last_name'     => (!empty($current_user->billing_last_name)) ? $current_user->billing_last_name : '',
            'email'         => (!empty($current_user->billing_email)) ? $current_user->billing_email : $current_user->user_email
        );
        $args = array(
            'defaults'      => $defaults,
            'order_id'      => (!empty($_GET['order'])) ? $_GET['order'] : '',
            'product_id'    => (!empty($_GET['product_id'])) ? $_GET['product_id'] : '',
            'idx'           => (!empty($_GET['idx'])) ? $_GET['idx'] : ''
        );

        ob_start();
        wc_get_template( 'shortcode-return-form.php', $args, 'warranty', WooCommerce_Warranty::$base_path .'/templates/' );

        return ob_get_clean();
    }

}

new Warranty_Shortcodes();
