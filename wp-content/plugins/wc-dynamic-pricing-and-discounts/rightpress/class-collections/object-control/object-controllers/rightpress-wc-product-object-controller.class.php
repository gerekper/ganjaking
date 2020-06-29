<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-wc-object-controller.class.php';
require_once 'interfaces/rightpress-wc-product-object-controller-interface.php';

/**
 * WooCommerce Product Wrapper Controller
 *
 * @class RightPress_WC_Product_Object_Controller
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_WC_Product_Object_Controller extends RightPress_WC_Object_Controller implements RightPress_WC_Product_Object_Controller_Interface
{

    // Change some other flags
    protected $is_editable = true;

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
