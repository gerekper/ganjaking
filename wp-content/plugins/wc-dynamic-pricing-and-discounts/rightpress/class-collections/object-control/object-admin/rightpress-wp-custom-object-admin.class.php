<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-wp-object-admin.class.php';

/**
 * WordPress Custom Object Admin Class
 *
 * @class RightPress_WP_Custom_Object_Admin
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_WP_Custom_Object_Admin extends RightPress_WP_Object_Admin
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
