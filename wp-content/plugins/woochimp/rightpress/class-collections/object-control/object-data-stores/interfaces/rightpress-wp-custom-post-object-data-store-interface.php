<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * WordPress Custom Post Object Data Store Interface
 *
 * @package RightPress
 * @author RightPress
 */
interface RightPress_WP_Custom_Post_Object_Data_Store_Interface
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
