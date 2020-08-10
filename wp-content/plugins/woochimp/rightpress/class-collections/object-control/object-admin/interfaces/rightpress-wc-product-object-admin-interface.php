<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * WooCommerce Product Admin Interface
 *
 * @package RightPress
 * @author RightPress
 */
interface RightPress_WC_Product_Object_Admin_Interface
{

    /**
     * Get checkbox label
     *
     * @access public
     * @return string
     */
    public function get_checkbox_label();

    /**
     * Get checkbox description
     *
     * @access public
     * @return string
     */
    public function get_checkbox_description();

    /**
     * Get product list custom column value
     *
     * @access public
     * @param int $post_id
     * @return string
     */
    public function get_product_list_shared_column_value($post_id);

    /**
     * Print product settings
     *
     * @access public
     * @return void
     */
    public function print_product_settings();

    /**
     * Print variation settings
     *
     * @access public
     * @param int $loop
     * @param array $variation_data
     * @param object $variation
     * @return void
     */
    public function print_variation_settings($loop, $variation_data, $variation);

    /**
     * Validate and sanitize product settings
     *
     * Throws RightPress_Exception in case of an error
     *
     * @access public
     * @param array $settings
     * @param object $object
     * @return array
     */
    public function sanitize_product_settings($settings, $object);





}
