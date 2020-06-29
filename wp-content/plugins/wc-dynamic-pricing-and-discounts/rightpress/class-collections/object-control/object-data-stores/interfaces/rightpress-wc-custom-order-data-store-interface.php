<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * WooCommerce Custom Order Data Store Interface
 *
 * @package RightPress
 * @author RightPress
 */
interface RightPress_WC_Custom_Order_Data_Store_Interface
{

    /**
     * Get post type
     *
     * @access public
     * @return string
     */
    public function get_post_type();

    /**
     * Get capability type
     *
     * @access public
     * @return string
     */
    public function get_capability_type();





}
