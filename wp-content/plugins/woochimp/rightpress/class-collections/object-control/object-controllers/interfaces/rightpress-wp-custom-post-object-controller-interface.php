<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * WordPress Custom Post Object Controller Interface
 *
 * @package RightPress
 * @author RightPress
 */
interface RightPress_WP_Custom_Post_Object_Controller_Interface
{

    /**
     * Get main post type
     *
     * Note: Foreign post type can be provided as main post type and the system must handle it correctly
     *
     * @access public
     * @return string
     */
    public function get_main_post_type();





}
