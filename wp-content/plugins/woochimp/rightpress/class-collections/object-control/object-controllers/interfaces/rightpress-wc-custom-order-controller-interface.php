<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * WooCommerce Custom Order Controller Interface
 *
 * @package RightPress
 * @author RightPress
 */
interface RightPress_WC_Custom_Order_Controller_Interface
{

    /**
     * Get post type
     *
     * @access public
     * @return string
     */
    public function get_post_type();

    /**
     * Get menu position
     *
     * @access public
     * @return int
     */
    public function get_menu_position();

    /**
     * Get menu icon
     *
     * @access public
     * @return string
     */
    public function get_menu_icon();

    /**
     * Get object class
     *
     * @access public
     * @return string
     */
    public function get_object_class();

    /**
     * Get data store class
     *
     * @access public
     * @return string
     */
    public function get_data_store_class();

    /**
     * Get post labels
     *
     * @access public
     * @return array
     */
    public function get_post_labels();





}
