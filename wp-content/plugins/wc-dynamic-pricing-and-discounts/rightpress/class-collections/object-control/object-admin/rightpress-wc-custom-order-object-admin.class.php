<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-wp-post-object-admin.class.php';

/**
 * WooCommerce Custom Order Object Admin
 *
 * @class RightPress_WC_Custom_Order_Object_Admin
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_WC_Custom_Order_Object_Admin extends RightPress_WP_Post_Object_Admin
{

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        // Call parent constructor
        parent::__construct();
    }





}
