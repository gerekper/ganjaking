<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-object-controller.class.php';

/**
 * WooCommerce Object Controller
 *
 * @class RightPress_WC_Object_Controller
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_WC_Object_Controller extends RightPress_Object_Controller
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
