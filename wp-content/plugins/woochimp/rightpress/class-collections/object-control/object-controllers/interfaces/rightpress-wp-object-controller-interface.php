<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * WordPress Object Controller Interface
 *
 * @package RightPress
 * @author RightPress
 */
interface RightPress_WP_Object_Controller_Interface
{

    /**
     * Get status list
     *
     * Status arrays must contain elements label and label_count
     *
     * @access public
     * @return array
     */
    public function get_status_list();

    /**
     * Get default status
     *
     * @access public
     * @return string
     */
    public function get_default_status();





}
