<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-object-controller.class.php';
require_once 'interfaces/rightpress-wp-object-controller-interface.php';

/**
 * WordPress Object Controller
 *
 * @class RightPress_WP_Object_Controller
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_WP_Object_Controller extends RightPress_Object_Controller implements RightPress_WP_Object_Controller_Interface
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
