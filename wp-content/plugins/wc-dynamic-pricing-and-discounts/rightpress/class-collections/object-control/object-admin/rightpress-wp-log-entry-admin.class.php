<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-wp-custom-post-object-admin.class.php';

/**
 * WordPress Custom Post Type Based Log Entry Admin
 *
 * @class RightPress_WP_Log_Entry_Admin
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_WP_Log_Entry_Admin extends RightPress_WP_Custom_Post_Object_Admin
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
