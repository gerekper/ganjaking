<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-object-admin.class.php';

/**
 * WordPress Object Admin
 *
 * @class RightPress_WP_Object_Admin
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_WP_Object_Admin extends RightPress_Object_Admin
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
