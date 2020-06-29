<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-wc-object-controller.class.php';
require_once 'interfaces/rightpress-wc-custom-order-object-controller-interface.php';

/**
 * WooCommerce Custom Order Object Controller
 *
 * @class RightPress_WC_Custom_Order_Object_Controller
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_WC_Custom_Order_Object_Controller extends RightPress_WC_Object_Controller implements RightPress_WC_Custom_Order_Object_Controller_Interface
{

    // Set flags
    protected $is_editable          = true;
    protected $is_chronologic       = true;
    protected $supports_metadata    = true;

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

    /**
     * Get status prefix
     *
     * @access public
     * @return string
     */
    public function get_status_prefix()
    {

        return 'wc-';
    }





}
